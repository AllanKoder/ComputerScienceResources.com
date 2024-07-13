<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\CommentClosure;
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
        // Clear the existing comments and closure table
        \DB::table('comments')->delete();
        \DB::table('comment_closures')->delete();

        // Seed comments for a specific resource
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);

        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
        $this->seedCommentsForResource(1);
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
        $comments = [];

        $comments[] = Comment::factory()->create([
            'comment_text' => 'This is a great resource!',
            'user_id' => 1,
            'commentable_id' => $resourceId, // resource comment
            'commentable_type' => Resource::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'Can you provide more details?',
            'user_id' => 3,
            'commentable_id' => $resourceId, // Another resource comment
            'commentable_type' => Resource::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'I found this very helpful.',
            'user_id' => 2,
            'commentable_id' => $comments[0]->id, // Reply to the first comment
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'I disagree with this point.',
            'user_id' => 4,
            'commentable_id' => $comments[2]->id, // Reply to the third comment
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'Thanks for sharing!',
            'user_id' => 5,
            'commentable_id' => $comments[2]->id,
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'Can you elaborate on this?',
            'user_id' => 6,
            'commentable_id' => $comments[2]->id, // Reply to the third comment
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'This was very informative.',
            'user_id' => 7,
            'commentable_id' => $comments[5]->id, 
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'I have a different perspective.',
            'user_id' => 8,
            'commentable_id' => $comments[6]->id, // Reply to the seventh comment
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'Great discussion!',
            'user_id' => 9,
            'commentable_id' => $comments[7]->id,
            'commentable_type' => Comment::class,
        ]);

        $comments[] = Comment::factory()->create([
            'comment_text' => 'I learned a lot from this.',
            'user_id' => 10,
            'commentable_id' => $comments[8]->id, // Reply to the ninth comment
            'commentable_type' => Comment::class,
        ]);

        // Insert closure table entries
        foreach ($comments as $comment) {
            $this->insertClosureTableEntries($comment);
        }
    }

    /**
     * Insert closure table entries for a comment.
     *
     * @param \App\Models\Comment $comment
     * @return void
     */
    private function insertClosureTableEntries(Comment $comment)
    {
        // Make a new closure for the new comment on the commentable model.
        if ($comment->commentable_type != Comment::class)
        {
            CommentClosure::create([
                'ancestor' => $comment->id,
                'comment_id' => $comment->id,
            ]);
            return;
        }

        // Find the ancestor ID of the commentable item
        $ancestorID = CommentClosure::where('comment_id', $comment->commentable_id)->first()->ancestor;

        // Create a new closure entry with the found ancestor ID
        CommentClosure::create([
            'ancestor' => $ancestorID,
            'comment_id' => $comment->id,
        ]);
    }
}
