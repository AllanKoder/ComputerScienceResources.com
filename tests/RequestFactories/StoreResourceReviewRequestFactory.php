<?php

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class StoreResourceReviewRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
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
