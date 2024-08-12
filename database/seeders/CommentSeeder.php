<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\CommentHierarchy;
use App\Models\Resource;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()   
    {
        // Seed comments for a specific resource
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(2);

    }

    /**
     * Seed comments for a specific resource.
     *
     * @param int $resourceId
     * @return void
     */
    private function seedCommentsForResource(int $resourceId)
    {
        $comments = [];

        $comments[] = Comment::factory()->create([
            'comment_text' => 'This is a great resource!',
            'commentable_id' => $resourceId, // resource comment
            'commentable_type' => Resource::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'Can you provide more details?',
            'commentable_id' => $resourceId, // Another resource comment
            'commentable_type' => Resource::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'I found this very helpful.',
            'commentable_id' => $comments[0]->id, // Reply to the first comment
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'I disagree with this point.',
            'commentable_id' => $comments[2]->id, // Reply to the third comment
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'Thanks for sharing!',
            'commentable_id' => $comments[2]->id,
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'Can you elaborate on this?',
            'commentable_id' => $comments[2]->id, // Reply to the third comment
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'This was very informative.',
            'commentable_id' => $comments[5]->id, 
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'I have a different perspective.',
            'commentable_id' => $comments[6]->id, // Reply to the seventh comment
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'Great discussion!',
            'commentable_id' => $comments[7]->id,
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'I learned a lot from this.',
            'commentable_id' => $comments[8]->id, // Reply to the ninth comment
            'commentable_type' => Comment::class,
        ]);

        // Insert closure table entries
        foreach ($comments as $comment) {
            $this::insertClosureTableEntries($comment);
        }
    }

    /**
     * Insert closure table entries for a comment.
     *
     * @param \App\Models\Comment $comment
     * @return void
     */
    public static function insertClosureTableEntries(Comment $comment)
    {
        // Make a new closure for the new comment on the commentable model.
        if ($comment->commentable_type != Comment::class)
        {
            CommentHierarchy::create([
                'ancestor' => $comment->id,
                'comment_id' => $comment->id,
            ]);
            return;
        }

        // Find the ancestor ID of the commentable item
        $ancestorID = CommentHierarchy::where('comment_id', $comment->commentable_id)->first()->ancestor;

        // Create a new closure entry with the found ancestor ID
        CommentHierarchy::create([
            'ancestor' => $ancestorID,
            'comment_id' => $comment->id,
        ]);
    }
}
