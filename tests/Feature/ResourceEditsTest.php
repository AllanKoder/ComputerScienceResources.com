<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\User;
use App\Services\ResourceEditsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\TestResources\ComputerScienceResourceTestResource;
use Illuminate\Http\UploadedFile;
use Storage;

class ResourceEditsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a default user to use in tests.
        $this->user = User::factory()->create();
    }

    public static function invalidResourceEditDataProvider(): array
    {
        return [
            'name too long' => ['name', str_repeat('a', 101)],
            'description too long' => ['description', str_repeat('a', 10001)],
            'invalid platform' => ['platforms', ['invalid_platform']],
            'invalid page_url' => ['page_url', 'not-a-url'],
            'invalid difficulty' => ['difficulty', 'invalid_difficulty'],
            'invalid pricing' => ['pricing', 'invalid_pricing'],
            'topic_tags too few' => ['topic_tags', ['tag1', 'tag2']],
            'invalid image_file' => ['image_file', 'not-a-file'],
            'general_tags not an array' => ['general_tags', 'not-an-array'],
            'programming_language_tags not an array' => ['programming_language_tags', 'not-an-array'],
        ];
    }

    /**
     * Test that invalid edit payloads are rejected.
     */
    #[DataProvider('invalidResourceEditDataProvider')]
    public function test_cannot_post_resource_with_invalid_fields(string $field, mixed $invalidValue): void
    {
        $this->actingAs($this->user);

        $resource = ComputerScienceResource::factory()->create();

        $testData['edit_title'] = 'title';
        $testData['edit_description'] = 'description';
        $testData['proposed_changes'] = [];
        $testData['proposed_changes'][$field] = $invalidValue;

        $response = $this->postJson(route('resource_edits.store', [
            'computerScienceResource' => $resource->id,
        ]), $testData);

        $this->assertTrue(
            $response->status() === 422,
            "Failed asserting that the server responded with a 422 status code for invalid '$field'. Response status: " . $response->status()
        );

        $notCreatedEdit = ResourceEdits::first();
        $this->assertNull(
            $notCreatedEdit,
            "Failed asserting that a resource edit was not created. Invalid field: " . $field
        );
    }

    /**
     * Test that submitting an edit that has no changes (relative to the original resource) is not allowed.
     */
    public function test_cannot_post_resource_edit_with_no_changes(): void
    {
        $this->actingAs($this->user);

        $times = 7;

        for ($i = 0; $i < $times; $i++)
        {
            // Create the original resource.
            $resource = ComputerScienceResource::factory()->create();

            // Create valid edit payload, then set fields to exactly match the resource.
            $editData = array();
            // Add required edit-specific fields.
            $editData['edit_title'] = 'Proposed edit with no changes';
            $editData['edit_description'] = 'This edit does nothing.';

            $editData['proposed_changes'] = [];

            $editData['proposed_changes']['name'] = $resource->name;
            $editData['proposed_changes']['description'] = $resource->description;

            $editData['proposed_changes']['page_url'] = $resource->page_url;
            $editData['proposed_changes']['platforms'] = $resource->platforms;
            $editData['proposed_changes']['difficulty'] = $resource->difficulty;
            $editData['proposed_changes']['pricing'] = $resource->pricing;
            $editData['proposed_changes']['topic_tags'] = $resource->topic_tags;
            $editData['proposed_changes']['programming_language_tags'] = $resource->programming_language_tags;
            $editData['proposed_changes']['general_tags'] = $resource->general_tags;

            $response = $this->post(route('resource_edits.store', $resource), $editData);

            $response->assertStatus(302);
            $response->assertSessionHas('warning', 'Cannot submit an edit with no changes made.');
        }
    }

        /**
     * Test that a valid resource edit can be posted.
     */
    public function test_can_post_valid_resource_edit(): void
    {
        $this->actingAs($this->user);

        // Create the original resource.
        $resource = ComputerScienceResource::factory()->create();

        // Create valid edit payload and change at least one attribute.
        $editData = ComputerScienceResourceTestResource::fake();
        $editData['edit_title'] = 'Proposed Update';
        $editData['edit_description'] = 'Proposing an update to the resource';

        $editData['proposed_changes']['name'] = $resource->name . ' Updated';

        $response = $this->post(route('resource_edits.store', $resource), $editData);

        // Expect redirection to the edit show page with a success message.
        $response->assertRedirect()
            ->assertSessionHas('success', "The proposed edits were created. Others can now view it.");

        $this->assertDatabaseHas('resource_edits', [
            'computer_science_resource_id' => $resource->id,
            'edit_title' => $editData['edit_title'],
        ]);
    }

    /**
     * Test that merging an edit updates the original resource.
     * We run multiple merges to simulate multiple edit merges.
     */

    public function test_merged_edit_reflects_changes_on_original_resource(): void
    {
        $resource = ComputerScienceResource::factory()->create();

        // Stub the ResourceEditsService to always allow merging
        $this->instance(
            ResourceEditsService::class,
            Mockery::mock(ResourceEditsService::class, function (MockInterface $mock) {
                $mock->shouldReceive('canMergeEdits')->andReturnTrue();
            })
        );

        $oldImagePath = $resource->image_path;

        $mergeAttempts = 10;

        for ($i = 0; $i < $mergeAttempts; $i++)
        {
            $resource->refresh();

            $editData = [];

            $editData['edit_title'] = "Edit #$i";
            $editData['edit_description'] = "This is edit number $i.";

            $editData['proposed_changes'] = [];
            $editData['proposed_changes']['name'] = "Resource Name Edited {$i}";
            $editData['proposed_changes']['description'] = "Resource Description Changed {$i}";
            $newImageFile = UploadedFile::fake()->image('resource.jpg');
            $editData['proposed_changes']['image_file'] = $newImageFile;
            $editData['proposed_changes']['page_url'] = "http://{$i}.com";
            $editData['proposed_changes']['difficulty'] = fake()->randomElement(config('computerScienceResource.difficulties'));
            $editData['proposed_changes']['platforms'] = fake()->randomElements(config('computerScienceResource.platforms'), fake()->numberBetween(1, 3));
            $editData['proposed_changes']['pricing'] = fake()->randomElement(config('computerScienceResource.pricings'));
            $editData['proposed_changes']['topic_tags'] = ["{$i}_a", "{$i}_b", "{$i}_c"];

            $editData['proposed_changes']['programming_language_tags'] = ["{$i}_a", "{$i}_b", "{$i}_c"];
            $editData['proposed_changes']['general_tags'] = ["{$i}_a", "{$i}_b", "{$i}_c"];

            // Submit the edit
            $this->actingAs($this->user);
            $response = $this->post(
                route('resource_edits.store', ['computerScienceResource' => $resource->id]),
                $editData
            );
            $response->assertStatus(302);

            $edit = ResourceEdits::latest()->first();
            $this->assertNotNull($edit, 'Failed to create resource edit');

            // Merge the edit
            $mergeResponse = $this->post(route('resource_edits.merge', ['resourceEdits' => $edit->id]));
            $mergeResponse
                ->assertRedirect(route('resources.show', ['computerScienceResource' => $resource->id]))
                ->assertSessionHas('success', 'Successfully merged new changed!');

            // Refresh and assert
            $resource->refresh();

            $this->assertEquals($editData['proposed_changes']['name'], $resource->name);
            $this->assertEquals($editData['proposed_changes']['description'], $resource->description);
            $this->assertNotNull($resource->image_path);
            $this->assertNotEquals($resource->image_path, $oldImagePath);
            $this->assertEquals($editData['proposed_changes']['page_url'], $resource->page_url);
            $this->assertEquals($editData['proposed_changes']['difficulty'], $resource->difficulty);
            $this->assertEquals($editData['proposed_changes']['pricing'], $resource->pricing);

            // Arrays
            $this->assertEqualsCanonicalizing($editData['proposed_changes']['platforms'], $resource->platforms);
            $this->assertEqualsCanonicalizing($editData['proposed_changes']['topic_tags'], $resource->topic_tags);
            $this->assertEqualsCanonicalizing($editData['proposed_changes']['programming_language_tags'], $resource->programming_language_tags);
            $this->assertEqualsCanonicalizing($editData['proposed_changes']['general_tags'], $resource->general_tags);

            $this->assertDatabaseMissing('resource_edits', ['id' => $edit->id]);
        }
    }

    public function test_merged_delete_image_edit_reflects_changes_on_original_resource(): void
    {
        $resource = ComputerScienceResource::factory()->create();

        // Stub the ResourceEditsService to always allow merging
        $this->instance(
            ResourceEditsService::class,
            Mockery::mock(ResourceEditsService::class, function (MockInterface $mock) {
                $mock->shouldReceive('canMergeEdits')->andReturnTrue();
            })
        );

        $editData['edit_title'] = "Edit";
        $editData['edit_description'] = "This is edit";

        $editData['proposed_changes'] = [];
        $editData['proposed_changes']['name'] = "Resource Name Edited";
        $editData['proposed_changes']['description'] = "Resource Description Changed";
        $editData['proposed_changes']['image_file'] = null; // Delete operation
        $editData['proposed_changes']['topic_tags'] = ['1', '2', '3'];
        $editData['proposed_changes']['programming_language_tags'] = [];
        $editData['proposed_changes']['general_tags'] = [];

        // Submit the edit
        $this->actingAs($this->user);
        $response = $this->post(
            route('resource_edits.store', ['computerScienceResource' => $resource->id]),
            $editData
        );
        $response->assertStatus(302);

        $edit = ResourceEdits::latest()->first();
        $this->assertNotNull($edit, 'Failed to create resource edit');

        // Merge the edit
        $mergeResponse = $this->post(route('resource_edits.merge', ['resourceEdits' => $edit->id]));
        $mergeResponse
            ->assertRedirect(route('resources.show', ['computerScienceResource' => $resource->id]))
            ->assertSessionHas('success', 'Successfully merged new changed!');

        // Refresh and assert
        $resource->refresh();

        $this->assertEquals("Resource Name Edited", $resource->name);
        $this->assertEquals("Resource Description Changed", $resource->description);
        $this->assertNull($resource->image_file); // Since we unset it

        // Changes
        $this->assertEqualsCanonicalizing($editData['proposed_changes']['topic_tags'], $resource->topic_tags);
        $this->assertEqualsCanonicalizing($editData['proposed_changes']['programming_language_tags'], $resource->programming_language_tags);
        $this->assertEqualsCanonicalizing($editData['proposed_changes']['general_tags'], $resource->general_tags);

        $this->assertDatabaseMissing('resource_edits', ['id' => $edit->id]);
    }
}
