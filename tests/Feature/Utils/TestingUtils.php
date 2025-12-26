<?php

namespace Tests\Feature\Utils;

use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\ResourceReview;
use App\Models\User;
use App\Services\ResourceEditsService;
use Mockery;
use Tests\RequestFactories\StoreCommentRequestFactory;
use Tests\RequestFactories\StoreResourceEditRequestFactory;
use Tests\RequestFactories\StoreResourceRequestFactory;
use Tests\RequestFactories\StoreResourceReviewRequestFactory;

trait TestingUtils
{
    public function createResource(array $overrides = []): ComputerScienceResource
    {
        $resourceForm = StoreResourceRequestFactory::new()->create($overrides);
        $response = $this->postJson(route('resources.store'), $resourceForm);
        $response->assertStatus(200);

        return ComputerScienceResource::where('name', $resourceForm['name'])->first();
    }

    public function createReview(int $id, array $overrides = [], bool $newUser = false): ResourceReview
    {
        if ($newUser) {
            $user = User::factory()->create();
            $this->actingAs($user);
        }

        $reviewForm = StoreResourceReviewRequestFactory::new()->create($overrides);
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
        $editData = StoreResourceEditRequestFactory::new()->create();
        if (! empty($changes)) {
            $editData['proposed_changes'] = array_merge($editData['proposed_changes'], $changes);
        }

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
        // Create a partial mock that only mocks canMergeEdits
        $service = Mockery::mock(ResourceEditsService::class)->makePartial();
        $service->shouldReceive('canMergeEdits')->andReturnTrue();

        $this->instance(ResourceEditsService::class, $service);

        // Merge the edit
        $mergeResponse = $this->post(route('resource_edits.merge', ['resourceEdits' => $edit->id]));
        $mergeResponse->assertRedirect();
    }

    public function createComment(string $commentableKey, int $commentableId, array $overrides = [])
    {
        $payload = StoreCommentRequestFactory::new()->create(array_merge([
            'commentable_key' => $commentableKey,
            'commentable_id' => $commentableId,
        ], $overrides));

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
