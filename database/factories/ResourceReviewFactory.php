<?php

namespace Database\Factories;

use App\Models\ComputerScienceResource;
use App\Models\ResourceReview;
use App\Models\UpvoteSummary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResourceReview>
 */
class ResourceReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id, // Is creating a new user since we need users to be unique per review
            'computer_science_resource_id' => function () {
                return ComputerScienceResource::firstOr(function () {
                    return ComputerScienceResource::factory()->create();
                })->id;
            },
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'community' => $this->faker->numberBetween(1, 5),
            'teaching_clarity' => $this->faker->numberBetween(1, 5),
            'engagement' => $this->faker->numberBetween(1, 5),
            'practicality' => $this->faker->numberBetween(1, 5),
            'user_friendliness' => $this->faker->numberBetween(1, 5),
            'updates' => $this->faker->numberBetween(1, 5),
            'pros' => $this->faker->words(mt_rand(1, 5)),
            'cons' => $this->faker->words(mt_rand(1, 5)),
        ];
    }
}
