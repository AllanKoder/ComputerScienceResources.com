<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\User;
use App\Services\ResourceEditsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $validData = ComputerScienceResourceTestResource::fake();

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
                'computerScienceResource' => $validData['id'],
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

        // Create the original resource.
        $resource = ComputerScienceResource::factory()->create();

        // Create valid edit payload, then set fields to exactly match the resource.
        $editData = ComputerScienceResourceTestResource::fake();
        $editData['name'] = $resource->name;
        $editData['description'] = $resource->description;
        $editData['page_url'] = $resource->page_url;
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
        
        $response->assertStatus(422);
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
        $editData['name'] = $resource->name . ' Updated';
        $editData['edit_title'] = 'Proposed Update';
        $editData['edit_description'] = 'Proposing an update to the resource';

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
        // Create a resource.
        $resource = ComputerScienceResource::factory()->create([
            'name' => 'Original Resource',
            'description' => 'Original description',
            'image_url' => 'http://example.com/original.png',
            'page_url' => 'http://example.com',
            'platforms' => ['web'],
            'difficulty' => 'beginner',
            'pricing' => 'free',
            'topic_tags' => ['laravel', 'php', 'testing'],
            'programming_language_tags' => ['php'],
            'general_tags' => ['resource']
        ]);

        // Stub the ResourceEditsService to always allow merging.
        $this->instance(ResourceEditsService::class, new class {
            public function canMergeEdits($resourceEdits)
            {
                return true;
            }
        });

        $mergeAttempts = 3;

        for ($i = 0; $i < $mergeAttempts; $i++) {
            // Create a unique edit for the same resource.
            $editData = ComputerScienceResourceTestResource::fake();
            $editData['name'] = $resource->name . ' Edited ' . $i;
            $editData['description'] = $resource->description . ' Now with change ' . $i;
            $editData['edit_title'] = "Edit #$i";
            $editData['edit_description'] = "This is change number $i.";

            // Post the edit.
            $this->actingAs($this->user)->post(route('resource_edits.store', $resource), $editData);
            // Retrieve the edit record.
            $resourceEdit = ResourceEdits::latest()->first();

            // Simulate merging the edit.
            $mergeResponse = $this->post(route('resource_edits.merge', $resourceEdit));

            $mergeResponse->assertRedirect(route('resources.show', ['computerScienceResource' => $resource->id]))
                ->assertSessionHas('success', 'Successfully merged new changed!');
            
            // Refresh the original resource
            $resource->refresh();

            // Assert that the original resource reflects the changes from the merged edit.
            $this->assertEquals($editData['name'], $resource->name);
            $this->assertEquals($editData['description'], $resource->description);

            // Check that the edit was deleted.
            $this->assertDatabaseMissing('resource_edits', [
                'id' => $resourceEdit->id,
            ]);
        }
    }
}
