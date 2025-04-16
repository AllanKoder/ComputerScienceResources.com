<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ComputerScienceResource;
use Database\Factories\ComputerScienceResourceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Tests\TestResources\ComputerScienceResourceTestResource;

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

        $formData = ComputerScienceResourceTestResource::fake();

        Log::debug("Form data: ". json_encode($formData));
        $response = $this->postJson(route('resources.store'), $formData);

        $response->assertStatus(302); // a redirect after successful creation
        $response->assertRedirect();

        // Check it is created
        $createdResource = ComputerScienceResource::where('name', $formData['name'])->first();
        $this->assertNotNull($createdResource);
    }

    public function test_cannot_post_resource_unauthed()
    {
        $formData = ComputerScienceResourceTestResource::fake();

        $response = $this->postJson(route('resources.store'), $formData);

        $response->assertStatus(401); // Auth required

        $createdResource = ComputerScienceResource::where('name', $formData['name'])->first();

        $this->assertNull($createdResource);
    }

    public function test_cannot_post_resource_with_long_name()
    {
        $this->actingAs($this->user);
        $formData = ComputerScienceResourceTestResource::fake();

        // Set to invalid name
        $formData['name'] = str_repeat('0', 101);

        $response = $this->postJson(route('resources.store'), $formData);

        $response->assertStatus(422); // a fail
    }

    public function test_cannot_post_resource_with_invalid_fields()
    {
        $this->actingAs($this->user);

        $validData = ComputerScienceResourceTestResource::fake();

        $invalidDataSets = [
            'name' => str_repeat('a', 101), # Too long
            'description' => str_repeat('a', 10001), # Too long
            'platforms' => ['invalid_platform'],
            'page_url' => 'not-a-url',
            'difficulty' => 'invalid_difficulty',
            'pricing' => 'invalid_pricing',
            'topic_tags' => ['tag1', 'tag2'], // Less than required minimum of 3
            'image_url' => 'not-a-url',
            'programming_language_tags' => null,
            'general_tags' => ['a','a','a'], // Not distinct
        ];

        // Choose from one of the invalid fields
        foreach ($invalidDataSets as $field => $invalidValue) {
            $testData = $validData;
            $testData[$field] = $invalidValue;

            $response = $this->postJson(route('resources.store'), $testData);

            $this->assertTrue(
                $response->status() === 422,
                "Failed asserting that the server responded with a 422 status code for invalid '$field'. Response status: " . $response->status()
            );

            $not_created_resource = ComputerScienceResource::first();
            $this->assertNull(
                $not_created_resource,
                "Failed asserting that a resource with name '{$testData['name']}' was not created in the database. Invalid field being: " . $field
            );
        }
    }
}
