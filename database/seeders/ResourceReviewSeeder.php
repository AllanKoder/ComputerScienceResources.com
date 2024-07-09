<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ResourceReview;
use App\Models\Comment;

class ResourceReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear the existing resource reviews table
        \DB::table('resource_reviews')->delete();
        
        // Create new resource reviews for resource 1
        $reviewsForResource1 = ResourceReview::factory()->count(5)->create([
            'resource_id' => 1,
        ]);
    
        // Create new resource reviews for resource 2
        $reviewsForResource2 = ResourceReview::factory()->count(5)->create([
            'resource_id' => 2,
        ]);
        
        $reviewsForResource1->each(function ($review) {
            $comment = Comment::factory()->create([
                'comment_text' => 'This is a comment for the review.',
                'user_id' => fake()->numberBetween(1, 10),
                'parent_id' => null,
                'commentable_id' => $review->id,
                'commentable_type' => ResourceReview::class,
            ]);
            $review->update(['comment_id' => $comment->id]);
    
            $review->comment()->save($comment);
        });
        
        $reviewsForResource2->each(function ($review) {
            $comment = Comment::factory()->create([
                'comment_text' => 'This is a comment for the review.',
                'user_id' => fake()->numberBetween(1, 10),
                'parent_id' => null,
                'commentable_id' => $review->id,
                'commentable_type' => ResourceReview::class,
            ]);
            $review->update(['comment_id' => $comment->id]);
    
            $review->comment()->save($comment);
        });        
        
        // Create new resource reviews for resource 1 with no comments
        ResourceReview::factory()->count(3)->create([
            'resource_id' => 1,
            'comment_id' => null,
        ]);
        
        // Create new resource reviews for resource 2 with no comments
        ResourceReview::factory()->count(3)->create([
            'resource_id' => 2,
            'comment_id' => null,
        ]);
        
    }
}
