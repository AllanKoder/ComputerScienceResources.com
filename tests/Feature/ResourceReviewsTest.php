<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestResources\ResourceReviewTestResource;

class ResourceReviewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_example(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_invalid_data_missing_required_fields(): void
    {
        $user = User::factory()->create();
        $resource = ComputerScienceResource::factory()->create();

        $invalidData = ResourceReviewTestResource::fake();
        unset($invalidData['title'], $invalidData['description']);

        $response = $this->actingAs($user)
            ->post(route('reviews.store', $resource), $invalidData);

        $response->assertSessionHasErrors(['title', 'description']);
    }

    public function test_invalid_data_invalid_rating(): void
    {
        $user = User::factory()->create();
        $resource = ComputerScienceResource::factory()->create();

        $invalidData = ResourceReviewTestResource::fake();
        $invalidData['community'] = 0;

        $response = $this->actingAs($user)
            ->post(route('reviews.store', $resource), $invalidData);

        $response->assertSessionHasErrors(['community']);
    }

    public function test_invalid_data_invalid_pros_field(): void
    {
        $user = User::factory()->create();
        $resource = ComputerScienceResource::factory()->create();

        $invalidData = ResourceReviewTestResource::fake();
        $invalidData['pros'] = 'Not an array';

        $response = $this->actingAs($user)
            ->post(route('reviews.store', $resource), $invalidData);

        $response->assertSessionHasErrors(['pros']);
    }

    public function test_resource_review_can_be_posted(): void
    {
        $user = User::factory()->create();
        $resource = ComputerScienceResource::factory()->create();

        $data = ResourceReviewTestResource::fake();

        $this->actingAs($user)
            ->post(route('reviews.store', $resource), $data);

        $this->assertDatabaseHas('resource_reviews', [
            'computer_science_resource_id' => $resource->id,
            'title' => $data['title']
        ]);
    }

    public function test_resource_review_cannot_be_posted_twice(): void
    {
        $user = User::factory()->create();
        $resource = ComputerScienceResource::factory()->create();

        $data1 = ResourceReviewTestResource::fake();
        $this->actingAs($user)
            ->post(route('reviews.store', $resource), $data1);

        $this->assertDatabaseHas('resource_reviews', [
            'computer_science_resource_id' => $resource->id,
            'title' => $data1['title']
        ]);

        $data2 = ResourceReviewTestResource::fake();
        $this->actingAs($user)
            ->post(route('reviews.store', $resource), $data2);
        
        $this->assertDatabaseMissing('resource_reviews', [
            'computer_science_resource_id' => $resource->id,
            'title' => $data2['title']
        ]);
    }

    public function test_resource_average_has_changed(): void
    {
        $user = User::factory()->create();
        $resource = ComputerScienceResource::factory()->create();

        $data = ResourceReviewTestResource::fake();

        $this->actingAs($user)
            ->post(route('reviews.store', $resource), $data);

        $this->assertDatabaseHas('resource_review_summaries', [
            'computer_science_resource_id' => $resource->id,
            'community' => $data['community'],
            'teaching_clarity' => $data['teaching_clarity'],
            'engagement' => $data['engagement'],
            'practicality' => $data['practicality'],
            'user_friendliness' => $data['user_friendliness'],
            'updates' => $data['updates'],
            'review_count' => 1,
        ]);
    }

    public function test_resource_review_average_updates_correctly(): void
    {
        $resource = ComputerScienceResource::factory()->create();
        $total = [
            'community' => 0,
            'teaching_clarity' => 0,
            'engagement' => 0,
            'practicality' => 0,
            'user_friendliness' => 0,
            'updates' => 0,
        ];

        $reviewCount = 5;

        for ($i = 0; $i < $reviewCount; $i++) {
            $user = User::factory()->create();
            $data = ResourceReviewTestResource::fake();

            // Add to total for averaging later
            foreach (array_keys($total) as $key) {
                $total[$key] += $data[$key];
            }

            $this->actingAs($user)->post(route('reviews.store', $resource), $data);
        }

        $this->assertDatabaseHas('resource_review_summaries', array_merge([
            'computer_science_resource_id' => $resource->id,
            'review_count' => $reviewCount,
        ], $total));
    }
}
