<?php

namespace Tests\Feature\Utils;

use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\ResourceReview;
use App\Models\User;
use App\Services\ResourceEditsService;
use Mockery;
use Mockery\MockInterface;
use Str;
use Tests\TestResources\ComputerScienceResourceTestResource;
use Tests\TestResources\ResourceReviewTestResource;

trait ResourceUtils
{
    public function createResource(array $overrides = []): ComputerScienceResource
    {
        $resourceForm = ComputerScienceResourceTestResource::fake($overrides);
        $response = $this->postJson(route('resources.store'), $resourceForm);
        $response->assertStatus(302); // Assert redirection is successful post
        return ComputerScienceResource::where('name', $resourceForm['name'])->first();
    }

    public function createReview(int $id, array $overrides = [], bool $newUser = false): ResourceReview
    {
        if ($newUser)
        {
            $user = User::factory()->create();
            $this->actingAs($user);
        }

        $reviewForm = ResourceReviewTestResource::fake($overrides);
        $response = $this->post(route('reviews.store', $id), $reviewForm);
        $response->assertStatus(200); // Success

        return ResourceReview::where('title', $reviewForm['title'])->first();
    }

    public function approveChanges($resourceId, $changes = [])
    {
        // Stub the ResourceEditsService to always allow merging
        $this->instance(
            ResourceEditsService::class,
            Mockery::mock(ResourceEditsService::class, function (MockInterface $mock) {
                $mock->shouldReceive('canMergeEdits')->andReturnTrue();
            })
        );

        $editData['edit_title'] = Str::uuid();
        $editData['edit_description'] = "This is edit";
        $editData['proposed_changes'] = [];
        foreach($changes as $key => $value)
        {
            $editData['proposed_changes'][$key] = $value;
        }

        // Submit the edit
        $this->actingAs($this->user);
        $response = $this->post(
            route('resource_edits.store', ['computerScienceResource' => $resourceId]),
            $editData
        );
        $response->assertStatus(302);
        $edit = ResourceEdits::where('edit_title', $editData['edit_title'])->get();

        $this->assertNotNull($edit, 'Failed to create resource edit');

        // Merge the edit
        $mergeResponse = $this->post(route('resource_edits.merge', ['resourceEdits' => $edit->id]));
        $mergeResponse
            ->assertRedirect(route('resources.show', ['computerScienceResource' => $resourceId]))
            ->assertSessionHas('success', 'Successfully merged new changed!');
    }
}
