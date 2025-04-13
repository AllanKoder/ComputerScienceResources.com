<?php

namespace Tests\Feature;

use App\Http\Resources\ComputerScienceResourceResource;
use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\User;
use App\Services\ResourceEditsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\TestResources\ComputerScienceResourceTestResource;

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

    /**
     * Test that invalid edit payloads are rejected.
     */
    public function test_cannot_post_resource_with_invalid_fields(): void
    {
        $this->actingAs($this->user);

        $resource = ComputerScienceResource::factory()->create();
        $validData = ComputerScienceResourceTestResource::fake();
        
        $validData['edit_title'] = 'title';
        $validData['edit_description'] = 'description';
        
        $invalidDataSets = [
            'name' => str_repeat('a', 101), // Too long
            'description' => str_repeat('a', 10001), // Too long
            'platforms' => ['invalid_platform'],
            'page_url' => 'not-a-url',
            'difficulty' => 'invalid_difficulty',
            'pricing' => 'invalid_pricing',
            'topic_tags' => ['tag1', 'tag2'], // Fewer than required minimum of 3
            'image_url' => 'not-a-url',
            'general_tags' => 'not-an-array',
            'programming_language_tags' => 'not-an-array'
        ];

        foreach ($invalidDataSets as $field => $invalidValue) {
            $testData = $validData;
            $testData[$field] = $invalidValue;

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
                "Failed asserting that a resource edit with name '{$testData['name']}' was not created. Invalid field: " . $field
            );
        }
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
            $editData['name'] = $resource->name;
            $editData['description'] = $resource->description;
            $editData['page_url'] = $resource->page_url;
            
            if ($resource->image_url)
            {
                $editData['image_url'] = $resource->image_url;
            }
            
            $editData['platforms'] = $resource->platforms;
            $editData['difficulty'] = $resource->difficulty;
            $editData['pricing'] = $resource->pricing;
            $editData['topic_tags'] = $resource->topic_tags;
            $editData['programming_language_tags'] = $resource->programming_language_tags;
            $editData['general_tags'] = $resource->general_tags;
            
            // Add required edit-specific fields.
            $editData['edit_title'] = 'Proposed edit with no changes';
            $editData['edit_description'] = 'This edit does nothing.';
            
            $response = $this->postJson(route('resource_edits.store', $resource), $editData);
            
            if ($response->status() !== 422) {
                Log::debug("here");
                Log::debug('editData:'. json_encode($editData));
                Log::debug('resource:'. json_encode(new ComputerScienceResourceResource($resource)));
            }
            
            $response->assertStatus(422);
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
        
        $editData['name'] = $resource->name . ' Updated';
        
        $response = $this->post(route('resource_edits.store', $resource), $editData);
        
        // Expect redirection to the edit show page with a success message.
        $response->assertRedirect()
            ->assertSessionHas('success', "The proposed edits were created. Other's can now view it.");

        $this->assertDatabaseHas('resource_edits', [
            'computer_science_resource_id' => $resource->id,
            'name' => $editData['name'],
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

        $mergeAttempts = 3;

        for ($i = 0; $i < $mergeAttempts; $i++) {
            $resource->refresh();

            $editData = ComputerScienceResourceTestResource::fake();

            $editData['edit_title'] = "Edit #$i";
            $editData['edit_description'] = "This is edit number $i.";

            $editData['name'] = "Resource Name Edited {$i}";
            $editData['description'] = "Resource Description Changed {$i}";
            
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

            $this->assertEquals($editData['name'], $resource->name, "Name did not update");
            $this->assertEquals($editData['description'], $resource->description, "Description did not update");

            $this->assertEquals(
                $editData['general_tags'],
                $resource->general_tags,
                "General tags did not update (expected " . json_encode($editData['general_tags']) . ", got " . json_encode($resource->general_tags) . ")"
            );

            $this->assertEquals(
                $editData['programming_language_tags'],
                $resource->programming_language_tags,
                "Programming language tags did not update (expected " . json_encode($editData['programming_language_tags']) . ", got " . json_encode($resource->programming_language_tags) . ")"
            );

            $this->assertDatabaseMissing('resource_edits', ['id' => $edit->id]);
        }
    }
}
