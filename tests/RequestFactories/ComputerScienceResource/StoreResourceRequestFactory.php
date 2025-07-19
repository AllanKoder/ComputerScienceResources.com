<?php

namespace Tests\RequestFactories\ComputerScienceResource;

use Worksome\RequestFactories\RequestFactory;

class StoreResourceRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
          // 'email' => $this->faker->email,
        ];
    }
}
