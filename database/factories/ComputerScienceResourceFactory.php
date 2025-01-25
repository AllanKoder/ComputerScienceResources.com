<?php

namespace Database\Factories;

use App\Models\ComputerScienceResource;
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
            'image_url' => fake()->imageUrl(),
            'page_url' => fake()->url(),
            'resource_created_on' => fake()->date(),

            'resource_type' => implode(',', 
                fake()->randomElements(['book', 'podcast', 'youtube channel', 'blog', 'website', 'organization', 'bootcamp', 'newsletter', 'workshop', 'course', 'forum', 'mobile app', 'desktop app', 'e-zine'], rand(1, 3))
            ),
            'difficulty' => fake()->randomElement(['beginner', 'industry_simple', 'industry_standard', 'industry_professional', 'academic']),
            'pricing' => fake()->randomElement(['free', 'premium', 'paid', 'freemium']),
        ];
    }
}
