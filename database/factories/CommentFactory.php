<?php

namespace Database\Factories;

use App\Models\ComputerScienceResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Comment;

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
        // Set the fixed commentable type.
        $commentableType = ComputerScienceResource::class;

        // Get a random ComputerScienceResource or create one if none exist.
        $resource = ComputerScienceResource::inRandomOrder()->first()
            ?? ComputerScienceResource::factory()->create();
        $commentableId = $resource->id;

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
            // Build the id_path: if the parent's depth is > 0, append parent's id to its id_path; else just use parent's id.
            $depth = $parent->depth + 1;
            $idPath = $parent->depth > 0 ? $parent->id_path . ',' . $parent->id : strval($parent->id);
        } else {
            // Top-level comment.
            $depth = 0;
            $idPath = "";
        }

        // Ignore children count for now

        return [
            'user_id' => $user->id,
            'content' => $this->faker->paragraph,
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'id_path' => $idPath,
            'depth' => $depth,
            'children_count' => 0,
        ];
    }
}
