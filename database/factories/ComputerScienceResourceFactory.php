<?php

namespace Database\Factories;

use App\Models\ComputerScienceResource;
use App\Models\User;
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
        return [
            'name' => fake()->name(),
            'description' => fake()->realText(),
            'user_id' => User::all()->random()->id,
            'image_url' => 'https://cdn.iconscout.com/icon/free/png-256/free-leetcode-logo-icon-download-in-svg-png-gif-file-formats--technology-social-media-company-vol-1-pack-logos-icons-3030025.png',
            'page_url' => fake()->url(),
            'resource_created_on' => fake()->date(),

            'resource_type' => implode(',', 
                fake()->randomElements(['book', 'podcast', 'youtube channel', 'blog', 'website', 'organization', 'bootcamp', 'newsletter', 'workshop', 'course', 'forum', 'mobile app', 'desktop app', 'e-zine'], rand(1, 3))
            ),
            'difficulty' => fake()->randomElement(['beginner', 'industry_simple', 'industry_standard', 'industry_professional', 'academic']),
            'pricing' => fake()->randomElement(['free', 'premium', 'paid', 'freemium']),
        ];
    }

    /**
     * Add tags to the model 
     */
    public function addTags(): Factory
    {
        // Define your tags here
        $tags = ['tag1','tag2','tag3','tag4','tag5'];

        // Create random tags for this resource
        $this->afterCreating(function (ComputerScienceResource $resource) use ($tags) {
            $resource->attachTags(fake()->randomElements($tags, rand(1, 3)));
        });

        return $this;
    }

}
