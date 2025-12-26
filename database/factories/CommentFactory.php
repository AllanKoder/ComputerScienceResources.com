<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

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

    // TODO: Double check this logic
    public function definition(): array
    {
        // Pick a random commentable type from config.
        $commentableKey = $this->faker->randomElement(['comment', 'resource']);
        $modelClass = Relation::getMorphedModel($commentableKey);

        // Use an existing user or create one.
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        // If the commentable type is a Comment, it means this new comment is a reply.
        if ($modelClass === Comment::class) {
            // Get an existing comment or create one if none exists.
            $existingComment = Comment::inRandomOrder()->first() ?? Comment::factory()->create();

            $commentableId = $existingComment->commentable_id;
            $commentableType = $existingComment->commentable_type;

            // Since it's a recursive comment, the existing comment becomes the parent.
            $parent = $existingComment;
            $parentCommentId = $parent->id;
            // If parent's depth is 1, it is the root; otherwise, use its stored root.
            $rootCommentId = ($parent->depth == 1) ? $parent->id : $parent->root_comment_id;
            $depth = $parent->depth + 1;
        } else {
            // For non-comment targets, fetch or create the commentable model.
            $commenting = $modelClass::inRandomOrder()->first() ?? $modelClass::factory()->create();
            $commentableId = $commenting->id;
            $commentableType = $commentableKey;

            // For non-comment targets we always create a top-level comment.
            $parentCommentId = null;
            $rootCommentId = null;
            $depth = 1;
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
}
