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

trait TestingUtils
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

    public function makeAndApplyResourceEdits($resourceId, $changes = [])
    {
        $edit = $this->createResourceEdit($resourceId, $changes);
        $this->approveResourceEdit($edit);
    }

    public function createResourceEdit($resourceId, $changes = []): ResourceEdits
    {
        if (empty($changes)) {
            $changes['name'] = 'default change';
        }

        $editData = [
            'edit_title' => Str::uuid(),
            'edit_description' => "This is a test edit.",
            'proposed_changes' => $changes,
        ];

        // Submit the edit
        $this->actingAs($this->user);
        $response = $this->postJson(
            route('resource_edits.store', ['computerScienceResource' => $resourceId]),
            $editData
        );
        $response->assertStatus(302);
        $edit = ResourceEdits::where('edit_title', $editData['edit_title'])->first();

        $this->assertNotNull($edit, 'Failed to create resource edit');

        return $edit;
    }

    public function approveResourceEdit(ResourceEdits $edit)
    {
        // Stub the ResourceEditsService to always allow merging
        $this->instance(
            ResourceEditsService::class,
            Mockery::mock(ResourceEditsService::class, function (MockInterface $mock) {
                $mock->shouldReceive('canMergeEdits')->andReturnTrue();
            })
        );

        // Merge the edit
        $mergeResponse = $this->post(route('resource_edits.merge', ['resourceEdits' => $edit->id]));
        $mergeResponse
            ->assertRedirect(route('resources.show', ['computerScienceResource' => $edit->computer_science_resource_id]))
            ->assertSessionHas('success', 'Successfully merged new changed!');
    }

    public function createComment(string $commentableKey, int $commentableId, array $overrides = [])
    {
        $payload = array_merge([
            'content' => fake()->sentence(),
            'commentable_key' => $commentableKey,
            'commentable_id' => $commentableId,
            'parent_comment_id' => null,
        ], $overrides);

        $response = $this->postJson(route('comments.store'), $payload);
        $response->assertStatus(200);

        return $response->json('new_comment');
    }

    public function upvote(string $typeKey, int $id)
    {
        $response = $this->postJson(route('upvote', ['typeKey' => $typeKey, 'id' => $id]));
        $response->assertStatus(200);
    }

    public function downvote(string $typeKey, int $id)
    {
        $response = $this->postJson(route('downvote', ['typeKey' => $typeKey, 'id' => $id]));
        $response->assertStatus(200);
    }
}
