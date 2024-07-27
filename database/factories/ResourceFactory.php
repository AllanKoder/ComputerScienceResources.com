<?php

namespace Database\Factories;

use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Resource::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $availableFormats = array_keys(config('formats'));
        $availablePricings = array_keys(config('pricings'));
        $availableDifficulties = array_keys(config('difficulties'));

        return [
            'user_id' => fake()->numberBetween(0, 10),
            'title' => fake()->sentence,
            'description' => fake()->paragraph,
            'image_url' => fake()->imageUrl,
            'formats' => fake()->randomElements($availableFormats, 2),
            'features' => fake()->randomElements(['Feature 1', 'Feature 2', 'Feature 3'], 2),
            'limitations' => fake()->randomElements(['Limitation 1', 'Limitation 2'], 2),
            'resource_url' => fake()->url,
            'pricing' => fake()->randomElement($availablePricings),
            'topics' => fake()->randomElements(['ASD AS A', 'Topic 2', 'Topic 3'], 2),
            'difficulty' => fake()->randomElement($availableDifficulties),
        ];
    }
}
