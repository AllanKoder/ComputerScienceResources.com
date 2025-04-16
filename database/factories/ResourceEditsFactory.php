<?php

namespace Database\Factories;

use App\Models\ResourceEdits;
use App\Models\User;
use App\Models\ComputerScienceResource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResourceEdits>
 */
class ResourceEditsFactory extends Factory
{
    protected $model = ResourceEdits::class;

    public function definition(): array
    {
        $platforms = config('computerScienceResource.platforms');
        $difficulties = config('computerScienceResource.difficulties');
        $pricings = config('computerScienceResource.pricings');

        return [
            'computer_science_resource_id' => function () {
                return ComputerScienceResource::inRandomOrder()->firstOr(function () {
                    return ComputerScienceResource::factory()->create();
                })->id;
            },
            'user_id' => function () {
                return User::inRandomOrder()->firstOr(function () {
                    return User::factory()->create();
                })->id;
            },
            'edit_title' => $this->faker->sentence,
            'edit_description' => $this->faker->paragraph,

            // Copied fields from the resource
            'name' => $this->faker->name(),
            'description' => $this->faker->realText(),
            'image_url' => 'https://cdn.iconscout.com/icon/free/png-256/free-leetcode-logo-icon-download-in-svg-png-gif-file-formats--technology-social-media-company-vol-1-pack-logos-icons-3030025.png',
            'page_url' => $this->faker->url(),

            'platforms' => $this->faker->randomElements($platforms, rand(1, 3)),
            'difficulty' => $this->faker->randomElement($difficulties),
            'pricing' => $this->faker->randomElement($pricings),

            'topic_tags' => ['data structures', 'algorithms'],
            'programming_language_tags' => ['python'],
            'general_tags' => ['interactive', 'challenging'],
        ];
    }
}
