<?php

namespace Tests\RequestFactories\ResourceEdit;

use Worksome\RequestFactories\RequestFactory;

class StoreResourceEditFactory extends RequestFactory
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

        $possibleChanges = [
            'name' => $this->faker->words(mt_rand(2, 4), true),
            'description' => $this->faker->paragraphs(2, true),
            'image_path' => 'https://cdn.iconscout.com/icon/free/png-256/free-leetcode-logo-icon-download-in-svg-png-gif-file-formats--technology-social-media-company-vol-1-pack-logos-icons-3030025.png',
            'page_url' => $this->faker->url(),
            'platforms' => $this->faker->randomElements($platforms, rand(1, 3)),
            'difficulty' => $this->faker->randomElement($difficulties),
            'pricing' => $this->faker->randomElement($pricings),
            'topic_tags' => array_values($topicTags),
            'programming_language_tags' => array_unique($this->faker->words(mt_rand(1, 3))),
            'general_tags' => array_unique($this->faker->words(mt_rand(1, 3))),
        ];

        $proposedKeys = $this->faker->randomElements(
            array_keys($possibleChanges),
            $this->faker->numberBetween(1, count($possibleChanges))
        );

        $proposedChanges = [];
        foreach ($proposedKeys as $key) {
            $proposedChanges[$key] = $possibleChanges[$key];
        }

        return [
            'edit_title' => $this->faker->sentence(),
            'edit_description' => $this->faker->paragraph(),
            'proposed_changes' => $proposedChanges,
        ];
    }
}
