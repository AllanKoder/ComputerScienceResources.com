<?php

namespace Database\Factories;

use App\Events\CommentCreated;
use App\Models\ComputerScienceResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Comment;
use App\Models\ResourceReview;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Set the random commentable type.
        $commentableType = $this->faker->randomElement([
            ResourceReview::class,
            Comment::class,
            ComputerScienceResource::class,
        ]);
        
        // Create the commented type
        $commenting = isset($models[$commentableType])
        ? $models[$commentableType]::inRandomOrder()->first() ?? $models[$commentableType]::factory()->create()
        : null;
    
        
        $commentableId = $commenting->id;

        // Get a random user (or create one if necessary).
        $user = User::inRandomOrder()->first()
            ?? User::factory()->create();

        // Randomly decide if this is a reply comment.
        $isReply = $this->faker->boolean;

        $parent = null;
        if ($isReply) {
            // Try to find an existing comment on the same resource.
            $parent = Comment::where('commentable_type', $commentableType)
                ->where('commentable_id', $commentableId)
                ->inRandomOrder()
                ->first();
        }

        if ($parent) {
            // Set the parent comment id.
            $parentCommentId = $parent->id;

            // Get the parent's root, and set that as this comment's root, unless it is the root itself.
            $rootCommentId = ($parent->depth == 0) ? $parent->id : $parent->root_comment_id;

            // Set the new depth.
            $depth = $parent->depth + 1;
        } else {
            // Top-level comment.
            $parentCommentId = null;
            $rootCommentId = null;
            $depth = 0;
        }

        return [
            'user_id' => $user->id,
            'content' => $this->faker->paragraph,
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'parent_comment_id' => $parentCommentId,
            'root_comment_id' => $rootCommentId,
            'depth' => $depth,
            'children_count' => 0,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Comment $comment) {
            if ($comment->root_comment_id) {
                Comment::where('id', $comment->root_comment_id)
                    ->increment('children_count');
            }

            CommentCreated::dispatch($comment->commentable_id, $comment->commentable_type);
        });
    }
}
