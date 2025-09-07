<?php

namespace Tests\RequestFactories;

use App\Models\ComputerScienceResource;
use Worksome\RequestFactories\RequestFactory;

class StoreCommentRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'content' => $this->faker->sentence(),
            'commentable_key' => 'resource',
            'commentable_id' => ComputerScienceResource::factory(),
            'parent_comment_id' => null,
        ];
    }
}
