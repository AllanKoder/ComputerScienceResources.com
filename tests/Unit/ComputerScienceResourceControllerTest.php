<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ComputerScienceResource;
use Database\Factories\ComputerScienceResourceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $resourceData = ComputerScienceResource::factory()->makeOne()->toArray();

        $response = $this->postJson(route('resources.store'), $resourceData);

        $response->assertStatus(302); // a redirect after successful creation
        $response->assertRedirect(route('resources'));

        $this->assertDatabaseHas('computer_science_resources', [
            'user_id' => $this->user->id,
            'name' => $resourceData['name'],
            'description' => $resourceData['description'],
            'page_url' => $resourceData['page_url'],
            'platforms' => $resourceData['platforms'],
            'difficulty' => $resourceData['difficulty'],
            'pricing' => $resourceData['pricing'],
        ]);

        $resource = ComputerScienceResource::where('name', $resourceData['name'])->first();
        $this->assertNotNull($resource);

        // Check tags
        $this->assertEquals($resourceData['topic_tags'], $resource->topic_tags);
        $this->assertEquals($resourceData['programming_languages'], $resource->programming_language_tags);
        $this->assertEquals($resourceData['general_tags'], $resource->general_tags);
    }

    public function test_can_post_resource_with_minimum_required_fields()
    {
        $this->actingAs($this->user);

        $resourceData = ComputerScienceResource::factory()->makeOne([
            'image_url' => null,
        ])->toArray();
        $resourceData['topics'] = ['Topic1', 'Topic2', 'Topic3'];

        // Remove optional fields
        unset($resourceData['generalTags']);
        unset($resourceData['programmingLanguages']);

        $response = $this->postJson(route('resources.store'), $resourceData);

        $response->assertStatus(302);
        $response->assertRedirect(route('resources'));

        $this->assertDatabaseHas('computer_science_resources', [
            'user_id' => $this->user->id,
            'name' => $resourceData['name'],
            'description' => $resourceData['description'],
            'page_url' => $resourceData['page_url'],
            'platforms' => $resourceData['platforms'],
            'difficulty' => $resourceData['difficulty'],
            'pricing' => $resourceData['pricing'],
            'image_url' => null,
        ]);

        $resource = ComputerScienceResource::where('name', $resourceData['name'])->first();
        $this->assertNotNull($resource);

        // Check tags
        $this->assertEquals($resourceData['topics'], $resource->topic_tags);
        $this->assertEmpty($resource->programming_language_tags);
        $this->assertEmpty($resource->general_tags);
    }
}
