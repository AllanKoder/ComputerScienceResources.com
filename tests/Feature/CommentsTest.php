<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use App\Models\ComputerScienceResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a top-level comment can be posted.
     */
    public function test_can_post_top_level_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = ComputerScienceResource::factory()->create();

        $payload = [
            'content' => 'This is a top level comment.',
            'commentable_type' => 'resource',
            'commentable_id' => $resource->id,
            'parent_comment_id' => null,
        ];

        $response = $this->postJson(route('comments.store'), $payload);
        $response->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'content' => 'This is a top level comment.',
            'commentable_id' => $resource->id,
            'parent_comment_id' => null,
        ]);
    }

    /**
     * Test that invalid comment data is rejected.
     */
    public function test_invalid_comment_data_not_allowed()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = ComputerScienceResource::factory()->create();

        // Omitting the 'content' field should trigger a validation error.
        $payload = [
            'commentable_type' => 'resource',
            'commentable_id' => $resource->id,
            'parent_comment_id' => null,
        ];

        $response = $this->postJson(route('comments.store'), $payload);
        $response->assertStatus(422);
    }

    /**
     * Test that comment on non-existent resource not allowed.
     */
    public function test_cannot_comment_on_non_existent_resource()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Resource does not exist
        $payload = [
            'content' => 'test',
            'commentable_type' => 'resource',
            'commentable_id' => 0, // does not exist
            'parent_comment_id' => null,
        ];

        $response = $this->postJson(route('comments.store'), $payload);
        $response->assertStatus(422);
    }

    /**
     * Test that comment on non-existent comment not allowed.
     */
    public function test_cannot_reply_on_non_existent_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Comment does not exist
        $payload = [
            'content' => 'test',
            'commentable_type' => 'comment',
            'commentable_id' => 0,
            'parent_comment_id' => null,
        ];

        $response = $this->postJson(route('comments.store'), $payload);
        $response->assertStatus(422);
    }

    /**
     * Test posting a valid nested comment (reply) within the maximum nesting depth.
     */
    public function test_can_post_nested_comment_within_max_depth()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = ComputerScienceResource::factory()->create();

        // Create a top-level comment first.
        $parentPayload = [
            'content' => 'Top level comment.',
            'commentable_type' => 'resource',
            'commentable_id' => $resource->id,
            'parent_comment_id' => null,
        ];
        $parentResponse = $this->postJson(route('comments.store'), $parentPayload);
        $parentResponse->assertStatus(200);
        $parentCommentId = $parentResponse->json('new_comment.id');

        // Post a reply to the top-level comment; its depth should be parent's depth + 1.
        $replyPayload = [
            'content' => 'This is a reply.',
            'commentable_type' => 'resource',
            'commentable_id' => $resource->id,
            'parent_comment_id' => $parentCommentId,
        ];
        $replyResponse = $this->postJson(route('comments.store'), $replyPayload);
        $replyResponse->assertStatus(200);
        $replyResponse->assertJsonFragment(['depth' => 2]);
    }

    /**
     * Test that a nested comment beyond the maximum depth is rejected.
     */
    public function test_cannot_post_nested_comment_exceeding_max_depth()
    {
        // For this test, set the maximum allowed nesting depth to 2.
        config(['comment.max_depth' => 2]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = ComputerScienceResource::factory()->create();

        // Create a top-level comment (depth 1).
        $parentPayload = [
            'content' => 'Top level comment.',
            'commentable_type' => 'resource',
            'commentable_id' => $resource->id,
            'parent_comment_id' => null,
        ];
        $parentResponse = $this->postJson(route('comments.store'), $parentPayload);
        $parentResponse->assertStatus(200);
        $parentCommentId = $parentResponse->json('new_comment.id');

        // Create a reply (depth 2) – this is allowed.
        $replyPayload = [
            'content' => 'Reply level 2.',
            'commentable_type' => 'resource',
            'commentable_id' => $resource->id,
            'parent_comment_id' => $parentCommentId,
        ];
        $replyResponse = $this->postJson(route('comments.store'), $replyPayload);
        $replyResponse->assertStatus(200);
        $replyCommentId = $replyResponse->json('new_comment.id');

        // Attempt to post a nested comment (would be depth 3) – should fail.
        $nestedReplyPayload = [
            'content' => 'Reply level 3 exceeds depth limit.',
            'commentable_type' => 'resource',
            'commentable_id' => $resource->id,
            'parent_comment_id' => $replyCommentId,
        ];
        $nestedReplyResponse = $this->postJson(route('comments.store'), $nestedReplyPayload);
        $nestedReplyResponse->assertStatus(422);
    }

    /**
     * Test that no more than the maximum allowed replies can be posted to a single root comment.
     */
    public function test_cannot_post_more_than_max_replies()
    {
        // For this test, set the maximum allowed replies for a root comment to 3.
        config(['comment.max_replies' => 3]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = ComputerScienceResource::factory()->create();

        // Create a top-level comment.
        $payload = [
            'content' => 'Top level comment for replies limit test.',
            'commentable_type' => 'resource',
            'commentable_id' => $resource->id,
            'parent_comment_id' => null,
        ];
        $response = $this->postJson(route('comments.store'), $payload);
        $response->assertStatus(200);
        $rootCommentId = $response->json('new_comment.id');

        // Post replies up to the allowed maximum.
        for ($i = 1; $i <= config('comment.max_replies'); $i++) {
            $replyPayload = [
                'content' => "Reply $i",
                'commentable_type' => 'resource',
                'commentable_id' => $resource->id,
                'parent_comment_id' => $rootCommentId,
            ];
            $replyResponse = $this->postJson(route('comments.store'), $replyPayload);
            $replyResponse->assertStatus(200);
        }

        // Attempt one more reply, which should be rejected.
        $extraReplyPayload = [
            'content' => 'This reply should fail due to reply limit.',
            'commentable_type' => 'resource',
            'commentable_id' => $resource->id,
            'parent_comment_id' => $rootCommentId,
        ];
        $extraReplyResponse = $this->postJson(route('comments.store'), $extraReplyPayload);
        $extraReplyResponse->assertStatus(422);
    }

    /**
     * Test paginated retrieval returns all comments via the show route.
     */
    public function test_paginated_comment_retrieval_returns_all_posted_comments()
    {
        config(['comment.pagination_limit' => 5]); // simulate a small page size

        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = ComputerScienceResource::factory()->create();

        // Create 3 top-level comments, each with 2 replies
        $postedCommentIds = [];

        for ($i = 1; $i <= 3; $i++) {
            $topPayload = [
                'content' => "Top level comment $i",
                'commentable_type' => 'resource',
                'commentable_id' => $resource->id,
                'parent_comment_id' => null,
            ];
            $topResponse = $this->postJson(route('comments.store'), $topPayload);
            $topResponse->assertStatus(200);
            $topId = $topResponse->json('new_comment.id');
            $postedCommentIds[] = $topId;

            for ($j = 1; $j <= 2; $j++) {
                $replyPayload = [
                    'content' => "Reply $j to comment $i",
                    'commentable_type' => 'resource',
                    'commentable_id' => $resource->id,
                    'parent_comment_id' => $topId,
                ];
                $replyResponse = $this->postJson(route('comments.store'), $replyPayload);
                $replyResponse->assertStatus(200);
                $postedCommentIds[] = $replyResponse->json('new_comment.id');
            }
        }

        // Now retrieve comments via the paginated route until all are fetched
        $retrievedCommentIds = [];
        $index = 0;

        do {
            $response = $this->getJson(route('comments.show', [
                'type' => 'resource',
                'id' => $resource->id,
                'index' => $index,
            ]));

            $response->assertStatus(200);
            $data = $response->json();

            $ids = collect($data['comments'])->pluck('id')->all();
            $retrievedCommentIds = array_merge($retrievedCommentIds, $ids);

            $index++;
        } while ($data['has_more_comments']);

        // Ensure all originally posted comment IDs are present
        $this->assertEqualsCanonicalizing(
            $postedCommentIds,
            $retrievedCommentIds
        );
    }
}
