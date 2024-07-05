<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory\App\Models\Review
 */
class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

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
            'comment_id' => fake()->numberBetween(1, 100),
            'resource_id' => fake()->numberBetween(1, 100),
            'user_id' => fake()->numberBetween(1, 50),
        ];
    }
}
