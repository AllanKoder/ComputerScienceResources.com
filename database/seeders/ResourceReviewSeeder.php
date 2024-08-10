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
        
        // Create new resource reviews for resource 1 with no comments
        $emptyCommentsReviewsForResource1 = ResourceReview::factory()->count(3)->create([
            'resource_id' => 1,
        ]);  
    }
}
