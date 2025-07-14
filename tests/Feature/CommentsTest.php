<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use App\Models\ComputerScienceResource;
use App\Models\UpvoteSummary;
use App\Services\ModelResolverService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Utils\TestingUtils;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;
    use TestingUtils;

    /**
     * Test a top-level comment can be posted.
     */
    public function test_can_post_top_level_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = ComputerScienceResource::factory()->create();

        $commentContent = 'This is a top level comment.';
        $this->createComment('resource', $resource->id, ['content' => $commentContent]);

        $this->assertDatabaseHas('comments', [
            'content' => $commentContent,
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
            'commentable_key' => 'resource',
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
            'commentable_key' => 'resource',
            'commentable_id' => 0, // does not exist
            'parent_comment_id' => null,
        ];

        $response = $this->postJson(route('comments.store'), $payload);
        $response->assertStatus(404);
    }

    /**
     * Test that commenting works on all commentable types defined in config.
     */
    public function test_can_comment_all_commentable_types()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        foreach (config('comment.commentable_keys') as $typeKey) {
            $modelClass = app(ModelResolverService::class)->getModelClass($typeKey);

            // Skip comments
            if ($modelClass === Comment::class) {
                continue;
            }

            $commentable = $modelClass::factory()->create();
            $commentContent = 'top level comment';
            $this->createComment($typeKey, $commentable->id, ['content' => $commentContent]);

            $this->assertDatabaseHas('comments', [
                'content' => $commentContent,
                'commentable_type' => $modelClass,
                'commentable_id' => $commentable->id,
            ]);
        }
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
            'commentable_key' => 'comment',
            'commentable_id' => 0,
            'parent_comment_id' => null,
        ];

        $response = $this->postJson(route('comments.store'), $payload);
        $response->assertStatus(404);
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
        $parentComment = $this->createComment('resource', $resource->id, ['content' => 'Top level comment.']);

        // Post a reply to the top-level comment.
        $replyComment = $this->createComment('resource', $resource->id, [
            'content' => 'This is a reply.',
            'parent_comment_id' => $parentComment['id'],
        ]);

        // The depth should be parent's depth + 1.
        $this->assertEquals(2, $replyComment['depth']);
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
        $parentComment = $this->createComment('resource', $resource->id, ['content' => 'Top level comment.']);

        // Create a reply (depth 2) – this is allowed.
        $replyComment = $this->createComment('resource', $resource->id, ['parent_comment_id' => $parentComment['id']]);

        // Attempt to post a nested comment (would be depth 3) – should fail.
        $nestedReplyPayload = [
            'content' => 'Reply level 3 exceeds depth limit.',
            'commentable_key' => 'resource',
            'commentable_id' => $resource->id,
            'parent_comment_id' => $replyComment['id'],
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
        $rootComment = $this->createComment('resource', $resource->id);

        // Post replies up to the allowed maximum.
        for ($i = 1; $i <= config('comment.max_replies'); $i++) {
            $this->createComment('resource', $resource->id, ['parent_comment_id' => $rootComment['id']]);
        }

        // Attempt one more reply, which should be rejected.
        $extraReplyResponse = $this->postJson(route('comments.store'), [
            'content' => 'This reply should fail due to reply limit.',
            'commentable_key' => 'resource',
            'commentable_id' => $resource->id,
            'parent_comment_id' => $rootComment['id'],
        ]);
        $extraReplyResponse->assertStatus(422);
    }

    /**
     * Test paginated retrieval returns all comments via the show route.
     */
    public function test_paginated_comment_retrieval_returns_all_posted_comments()
    {
        config(['comment.pagination_limit' => 5]); // simulate a small page size
        config(['comment.default_pagination_limit' => 2]); // simulate a small page size

        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = ComputerScienceResource::factory()->create();

        // Create 3 top-level comments, each with 2 replies
        $postedCommentIds = [];

        for ($i = 1; $i <= 3; $i++) {
            $topComment = $this->createComment('resource', $resource->id);
            $postedCommentIds[] = $topComment['id'];

            for ($j = 1; $j <= 2; $j++) {
                $replyComment = $this->createComment('resource', $resource->id, ['parent_comment_id' => $topComment['id']]);
                $postedCommentIds[] = $replyComment['id'];
            }
        }

        // Now retrieve comments via the paginated route until all are fetched
        $retrievedCommentIds = [];
        $index = 0;

        do {
            $response = $this->getJson(route('comments.show', [
                'commentableKey' => 'resource',
                'commentableId' => $resource->id,
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

    /**
     * Test that upvote summaries are created for all comments (root and replies).
     */
    public function test_upvote_summaries_created_for_all_comments()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $resource = ComputerScienceResource::factory()->create();

        // Create a root comment
        $rootComment = $this->createComment('resource', $resource->id);

        // Verify upvote summary was created for root comment
        $this->assertDatabaseHas('upvote_summaries', [
            'upvotable_id' => $rootComment['id'],
            'upvotable_type' => Comment::class,
        ]);

        // Create a reply comment
        $replyComment = $this->createComment('resource', $resource->id, ['parent_comment_id' => $rootComment['id']]);

        // Verify upvote summary was created for reply comment
        $this->assertDatabaseHas('upvote_summaries', [
            'upvotable_id' => $replyComment['id'],
            'upvotable_type' => Comment::class,
        ]);

        // Also test commenting on a comment directly
        $commentOnComment = $this->createComment('comment', $rootComment['id']);

        // Verify upvote summary was created for comment on comment
        $this->assertDatabaseHas('upvote_summaries', [
            'upvotable_id' => $commentOnComment['id'],
            'upvotable_type' => Comment::class,
        ]);

        // Verify that we have exactly 3 upvote summaries for comments
        $upvoteSummariesCount = UpvoteSummary::where('upvotable_type', Comment::class)->count();
        $this->assertEquals(3, $upvoteSummariesCount);
    }

    /**
     * Test that upvote summaries and upvotes are cleaned up when comments are deleted.
     */
    public function test_upvote_summaries_and_upvotes_cleaned_up_when_comments_deleted()
    {
        $this->actingAs(User::factory()->create());

        $resource = ComputerScienceResource::factory()->create();

        // Create a comment and get its data
        $commentData = $this->createComment('resource', $resource->id);
        $commentId = $commentData['id'];

        // Upvote the comment
        $this->upvote('comment', $commentId);

        // Verify upvote and summary exist
        $this->assertDatabaseHas('upvotes', [
            'upvotable_id' => $commentId,
            'upvotable_type' => Comment::class,
        ]);
        $this->assertDatabaseHas('upvote_summaries', [
            'upvotable_id' => $commentId,
            'upvotable_type' => Comment::class,
        ]);

        // Delete the comment
        Comment::find($commentId)->delete();

        // Verify upvote and summary were deleted
        $this->assertDatabaseMissing('upvotes', [
            'upvotable_id' => $commentId,
            'upvotable_type' => Comment::class,
        ]);
        $this->assertDatabaseMissing('upvote_summaries', [
            'upvotable_id' => $commentId,
            'upvotable_type' => Comment::class,
        ]);
    }
}
