<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ResourceReview;
use App\Models\Resource;
use App\Models\User;

class ResourceReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create new resource reviews for resource 1
        $resources = Resource::take(15)->get();
        $users = User::limit(4)->get(); // Limit to the first 2 users

        foreach ($resources as $resource) {
            foreach ($users as $user) {
                // Check if the user has already reviewed the resource
                if (!$user->resourceReviews()->where('resource_id', $resource->id)->exists()) {
                    ResourceReview::factory()->create([
                        'resource_id' => $resource->id,
                        'user_id' => $user->id,
                    ]);
                }
            }
        }
    }
}
