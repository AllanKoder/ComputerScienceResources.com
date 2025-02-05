<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ComputerScienceResource;
use Database\Factories\ComputerScienceResourceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ComputerScienceResourceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_post_resource()
    {
        $this->actingAs($this->user);

        $resourceData = ComputerScienceResource::factory()->addTags()->create();
        $formData = $resourceData->toFormRequestArray();

        $response = $this->postJson(route('resources.store'), $formData);

        $response->assertStatus(302); // a redirect after successful creation
        $response->assertRedirect(route('resources'));

        $createdResource = ComputerScienceResource::where('name', $formData['name'])->first();

        $this->assertNotNull($createdResource);

        // Check tags
        $this->assertEquals($formData['topic_tags'], $createdResource->topic_tags);
        $this->assertEquals($formData['programming_language_tags'], $createdResource->programming_language_tags);
        $this->assertEquals($formData['general_tags'], $createdResource->general_tags);
    }

    public function test_cannot_post_resource_unauthed()
    {
        $resourceData = ComputerScienceResource::factory()->addTags()->create();
        $formData = $resourceData->toFormRequestArray();

        $response = $this->postJson(route('resources.store'), $formData);

        $response->assertStatus(401); // Auth required

        $createdResource = ComputerScienceResource::where('name', $formData['name'])->first();

        $this->assertNull($createdResource);
    }

    public function test_cannot_post_resource_with_long_name()
    {
        $this->actingAs($this->user);

        $resourceData = ComputerScienceResource::factory()->addTags()->create();
        $formData = $resourceData->toFormRequestArray();
        $formData['name'] = str_repeat('0', 101);

        $response = $this->postJson(route('resources.store'), $formData);

        $response->assertStatus(422); // a fail
    }

    public function test_cannot_post_resource_with_invalid_fields()
    {
        $this->actingAs($this->user);

        $validData = ComputerScienceResource::factory()->addTags()->make()->toFormRequestArray();

        $invalidDataSets = [
            'name' => str_repeat('a', 101), # Too long
            'description' => str_repeat('a', 4001), # Too long
            'platforms' => 'invalid_platform',
            'page_url' => 'not-a-url',
            'difficulty' => 'invalid_difficulty',
            'pricing' => 'invalid_pricing',
            'topic_tags' => ['tag1', 'tag2'], // Less than required minimum of 3
            'image_url' => 'not-a-url',
            'general_tags' => 'not-an-array',
            'programming_language_tags' => 'not-an-array'
        ];

        foreach ($invalidDataSets as $field => $invalidValue) {
            $testData = $validData;
            $testData[$field] = $invalidValue;

            $response = $this->postJson(route('resources.store'), $testData);

            $this->assertTrue(
                $response->status() === 422,
                "Failed asserting that the server responded with a 422 status code for invalid '$field'. Response status: " . $response->status()
            );

            $this->assertTrue(
                $response->json('errors.' . $field) !== null,
                "Failed asserting that the response contains a validation error for '$field'"
            );

            $errorFound = false;
            $errorMessage = '';

            // Check for direct field error
            if ($response->json('errors.' . $field) !== null) {
                $errorFound = true;
                $errorMessage = $response->json('errors.' . $field)[0] ?? 'No specific error message';
            }

            // Check for array field error (e.g., platforms.0)
            if (!$errorFound && $response->json('errors.' . $field . '.0') !== null) {
                $errorFound = true;
                $errorMessage = $response->json('errors.' . $field . '.0')[0] ?? 'No specific error message';
            }

            $this->assertTrue($errorFound, "Failed asserting that the response contains a validation error for '$field'. " . $errorMessage);

            $resource = ComputerScienceResource::first();
            $this->assertNull(
                $resource,
                "Failed asserting that a resource with name '{$testData['name']}' was not created in the database. Invalid field being: " . $field
            );
        }
    }
}
