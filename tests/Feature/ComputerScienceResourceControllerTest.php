<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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

        $response = $this->postJson(route('resources.store'), $formData);

        $response->assertRedirect(); // a redirect after successful creation

        // Check it is created
        $createdResource = ComputerScienceResource::where('name', $formData['name'])->first();
        $this->assertNotNull($createdResource);
    }

    public function test_can_post_resource_with_image()
    {
        Storage::fake('public');
        $this->actingAs($this->user);

        $formData = ComputerScienceResourceTestResource::fake();
        $formData['image_file'] = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post(route('resources.store'), $formData);

        $response->assertRedirect(); // a redirect after successful creation

        // Check it is created
        $createdResource = ComputerScienceResource::where('name', $formData['name'])->first();
        $this->assertNotNull($createdResource);
        $this->assertNotNull($createdResource->image_path);
        Storage::disk('public')->assertExists('resource/'.$formData['image_file']->hashName());
    }

    public function test_cannot_post_resource_unauthed()
    {
        $formData = ComputerScienceResourceTestResource::fake();

        $response = $this->postJson(route('resources.store'), $formData);

        $response->assertStatus(401); // Auth required

        $createdResource = ComputerScienceResource::where('name', $formData['name'])->first();

        $this->assertNull($createdResource);
    }

    public static function invalidFieldProvider(): array
    {
        return [
            'name too long' => ['name', str_repeat('a', 101)],
            'description too long' => ['description', str_repeat('a', 10001)],
            'invalid platform' => ['platforms', ['invalid_platform']],
            'invalid page_url' => ['page_url', 'not-a-url'],
            'invalid difficulty' => ['difficulty', 'invalid_difficulty'],
            'invalid pricing' => ['pricing', 'invalid_pricing'],
            'too few topic_tags' => ['topic_tags', ['tag1', 'tag2']],
            'invalid image_file' => ['image_file', 'not-an-image'],
            'null programming_language_tags' => ['programming_language_tags', null],
            'non-distinct general_tags' => ['general_tags', ['a', 'a', 'a']],
        ];
    }

    #[DataProvider('invalidFieldProvider')]
    #[Test]
    #[Group('slow')]
    public function test_cannot_post_resource_with_invalid_fields(string $field, mixed $invalidValue)
    {
        $this->actingAs($this->user);

        $validData = ComputerScienceResourceTestResource::fake();
        $validData[$field] = $invalidValue;

        $response = $this->postJson(route('resources.store'), $validData);

        $this->assertEquals(
            422,
            $response->status(),
            "Failed asserting that the server responded with a 422 status code for invalid '$field'. Response status: ".$response->status()
        );

        $not_created_resource = ComputerScienceResource::first();
        $this->assertNull(
            $not_created_resource,
            "Failed asserting that a resource with name '{$validData['name']}' was not created in the database. Invalid field: $field"
        );
    }
}
