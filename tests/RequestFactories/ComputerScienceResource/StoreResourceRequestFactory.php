<?php

namespace Tests\RequestFactories\ComputerScienceResource;

use Worksome\RequestFactories\RequestFactory;

class StoreResourceRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        $platforms = config('computerScienceResource.platforms');
        $difficulties = config('computerScienceResource.difficulties');
        $pricings = config('computerScienceResource.pricings');

        // Ensure at least 3 unique topic tags
        do {
            $topicTags = array_unique($this->faker->words(mt_rand(4, 9)));
        } while (count($topicTags) < 3);

        return [
            'name' => fake()->name(),
            'description' => fake()->realText(),
            'page_url' => fake()->url(),
            'platforms' => fake()->randomElements($platforms, rand(1, 3)),
            'difficulty' => fake()->randomElement($difficulties),
            'pricing' => fake()->randomElement($pricings),
            'topic_tags' => array_values($topicTags),
            'programming_language_tags' => array_unique($this->faker->words(mt_rand(1, 5))),
            'general_tags' => array_unique($this->faker->words(mt_rand(2, 6))),
        ];
    }

    public function files(): array
    {
        return [
            'image_path' => $this->file()->image('test.png', 200, 200),
        ];
    }
}
