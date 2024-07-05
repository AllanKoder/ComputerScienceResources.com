<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear the existing comments table
        \DB::table('comments')->delete();

        // Seed comments for a specific resource
        $this->seedCommentsForResource(1);
    }

    /**
     * Seed comments for a specific resource.
     *
     * @param int $resourceId
     * @return void
     */
    private function seedCommentsForResource(int $resourceId)
    {
        Comment::factory()->create([
            'comment_text' => 'This is a great resource!',
            'user_id' => 1,
            'parent_id' => null, // Top-level comment
            'commentable_id' => $resourceId,
            'commentable_type' => 'App\\Models\\Resource',
        ]);

        Comment::factory()->create([
            'comment_text' => 'I found this very helpful.',
            'user_id' => 2,
            'parent_id' => 1, // Reply to the first comment
            'commentable_id' => $resourceId,
            'commentable_type' => 'App\\Models\\Resource',
        ]);

        Comment::factory()->create([
            'comment_text' => 'Can you provide more details?',
            'user_id' => 3,
            'parent_id' => null, // Another top-level comment
            'commentable_id' => $resourceId,
            'commentable_type' => 'App\\Models\\Resource',
        ]);

        Comment::factory()->create([
            'comment_text' => 'I disagree with this point.',
            'user_id' => 4,
            'parent_id' => 1, // Reply to the first comment
            'commentable_id' => $resourceId,
            'commentable_type' => 'App\\Models\\Resource',
        ]);

        Comment::factory()->create([
            'comment_text' => 'Thanks for sharing!',
            'user_id' => 5,
            'parent_id' => null, // Top-level comment
            'commentable_id' => $resourceId,
            'commentable_type' => 'App\\Models\\Resource',
        ]);

        Comment::factory()->create([
            'comment_text' => 'Can you elaborate on this?',
            'user_id' => 6,
            'parent_id' => 3, // Reply to the third comment
            'commentable_id' => $resourceId,
            'commentable_type' => 'App\\Models\\Resource',
        ]);

        Comment::factory()->create([
            'comment_text' => 'This was very informative.',
            'user_id' => 7,
            'parent_id' => null, // Top-level comment
            'commentable_id' => $resourceId,
            'commentable_type' => 'App\\Models\\Resource',
        ]);

        Comment::factory()->create([
            'comment_text' => 'I have a different perspective.',
            'user_id' => 8,
            'parent_id' => 7, // Reply to the seventh comment
            'commentable_id' => $resourceId,
            'commentable_type' => 'App\\Models\\Resource',
        ]);

        Comment::factory()->create([
            'comment_text' => 'Great discussion!',
            'user_id' => 9,
            'parent_id' => null, // Top-level comment
            'commentable_id' => $resourceId,
            'commentable_type' => 'App\\Models\\Resource',
        ]);

        Comment::factory()->create([
            'comment_text' => 'I learned a lot from this.',
            'user_id' => 10,
            'parent_id' => 9, // Reply to the ninth comment
            'commentable_id' => $resourceId,
            'commentable_type' => 'App\\Models\\Resource',
        ]);
    }
}
