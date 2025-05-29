<?php

namespace Tests\Feature\Utils;

use App\Models\ComputerScienceResource;
use App\Models\ResourceReview;
use App\Models\User;
use Tests\TestResources\ComputerScienceResourceTestResource;
use Tests\TestResources\ResourceReviewTestResource;

trait ResourceUtils
{
    public function createResource(array $overrides = []): ComputerScienceResource
    {
        $resourceForm = ComputerScienceResourceTestResource::fake($overrides);
        $response = $this->postJson(route('resources.store'), $resourceForm);
        $response->assertStatus(302); // Assert redirection is successful post
        return ComputerScienceResource::where('name', $resourceForm['name'])->first();
    }

    public function createReview(int $id, array $overrides = [], bool $newUser = false): ResourceReview
    {
        if ($newUser)
        {
            $user = User::factory()->create();
            $this->actingAs($user);
        }

        $reviewForm = ResourceReviewTestResource::fake($overrides);
        $response = $this->post(route('reviews.store', $id), $reviewForm);
        $response->assertStatus(200); // Success

        return ResourceReview::where('title', $reviewForm['title'])->first();
    }
}
