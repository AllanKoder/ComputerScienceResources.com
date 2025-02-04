<?php

namespace Database\Factories;

use App\Models\ComputerScienceResource;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComputerScienceResource>
 */
class ComputerScienceResourceFactory extends Factory
{
    protected $model = ComputerScienceResource::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $platforms = config('computerScienceResource.platforms');
        $difficulties = config('computerScienceResource.difficulties');
        $pricings = config('computerScienceResource.pricings');

        return [
            'name' => fake()->name(),
            'description' => fake()->realText(),
            'user_id' => User::all()->random()->id,
            'image_url' => 'https://cdn.iconscout.com/icon/free/png-256/free-leetcode-logo-icon-download-in-svg-png-gif-file-formats--technology-social-media-company-vol-1-pack-logos-icons-3030025.png',
            'page_url' => fake()->url(),

            'platforms' => implode(
                ',',
                fake()->randomElements($platforms, rand(1, 3))
            ),
            'difficulty' => fake()->randomElement($difficulties),
            'pricing' => fake()->randomElement($pricings),
        ];
    }

    /**
     * Add tags to the model 
     */
    public function addTags(): Factory
    {
        $types = ['topics','programming_languages', 'tags'];

        // Define your tags here
        $tags = ['tag1', 'tag2', 'tag3', 'tag4', 'tag5', fake()->name(), fake()->name()];

        // Create random tags for this resource
        return $this->afterCreating(function (ComputerScienceResource $resource) use ($tags, $types) {
            $tagElements = fake()->randomElements($tags, rand(1, 3));
            $resource->attachTags($tagElements, fake()->randomElement($types));
        });
    }
}
