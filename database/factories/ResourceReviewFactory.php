<?php

namespace Database\Factories;

use App\Models\ResourceReview;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Resource;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory\App\Models\ResourceReview
 */
class ResourceReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ResourceReview::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Get a random resource or create one if none exist
        $resource = Resource::inRandomOrder()->first() ?? Resource::factory()->create();

        // Get a user who hasn't reviewed this resource yet
        $user = User::whereDoesntHave('resourceReviews', function ($query) use ($resource) {
            $query->where('resource_id', $resource->id);
        })->inRandomOrder()->first();

        // If all users have reviewed the resource, create a new user
        if (!$user) {
            $user = User::factory()->create();
        }

        return [
            'community_size' => fake()->numberBetween(0, 5),
            'teaching_explanation_clarity' => fake()->numberBetween(0, 5),
            'practicality_to_industry' => fake()->numberBetween(0, 5),
            'technical_depth' => fake()->numberBetween(0, 5),
            'user_friendliness' => fake()->numberBetween(0, 5),
            'updates_and_maintenance' => fake()->numberBetween(0, 5),
            'review_title' => fake()->sentence,
            'review_description' => fake()->paragraph,
            'resource_id' => $resource->id,
            'user_id' => $user->id,
        ];
    }
}
