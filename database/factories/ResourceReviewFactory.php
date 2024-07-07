<?php

namespace Database\Factories;

use App\Models\ResourceReview;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'community_size' => fake()->numberBetween(0, 5),
            'teaching_explanation_clarity' => fake()->numberBetween(0, 5),
            'practicality_to_industry' => fake()->numberBetween(0, 5),
            'technical_depth' => fake()->numberBetween(0, 5),
            'user_friendliness' => fake()->numberBetween(0, 5),
            'updates_and_maintenance' => fake()->numberBetween(0, 5),
            'comment_id' => null,
            'resource_id' => fake()->numberBetween(1, 3),
            'user_id' => fake()->numberBetween(1, 10),
        ];
    }
}
