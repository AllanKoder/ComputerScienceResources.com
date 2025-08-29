<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use App\Models\TagFrequency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Utils\TestingUtils;
use Tests\RequestFactories\ComputerScienceResource\StoreResourceRequestFactory;
use Tests\TestCase;

class ComputerScienceResourceTest extends TestCase
{
    use RefreshDatabase;
    use TestingUtils;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_post_resource()
    {
        $this->actingAs($this->user);

        $formData = StoreResourceRequestFactory::new()->create();

        $response = $this->postJson(route('resources.store'), $formData);

        $response->assertStatus(200);

        // Check it is created
        $createdResource = ComputerScienceResource::where('name', $formData['name'])->first();
        $this->assertNotNull($createdResource);
    }

    public function test_can_post_resource_with_image()
    {
        Storage::fake('public');
        $this->actingAs($this->user);

        $formData = StoreResourceRequestFactory::new()->create();
        $formData['image_file'] = UploadedFile::fake()->image('resource_image.jpg');

        $response = $this->post(route('resources.store'), $formData);

        $response->assertStatus(200);

        // Check it is created
        $createdResource = ComputerScienceResource::where('name', $formData['name'])->first();
        $this->assertNotNull($createdResource);
        $this->assertNotNull($createdResource->image_path);
        Storage::disk('public')->assertExists('resource/'.$formData['image_file']->hashName());
    }

    public function test_cannot_post_resource_unauthed()
    {
        $formData = StoreResourceRequestFactory::new()->create();

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
            'too few topic_tags' => ['topic_tags', ['tag1']],
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

        $validData = StoreResourceRequestFactory::new()->create();
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

    public function test_image_is_removed_if_resource_creation_fails()
    {
        Storage::fake('public');
        $this->actingAs($this->user);

        // Create valid form data but set an invalid field to force failure
        $formData = StoreResourceRequestFactory::new()->create();
        $formData['image_file'] = UploadedFile::fake()->image('fail_image.jpg');
        $formData['name'] = str_repeat('a', 101); // Invalid name, will fail validation

        $response = $this->postJson(route('resources.store'), $formData);
        $response->assertStatus(422); // Validation error

        // The image should not exist in storage
        $this->assertEmpty(Storage::disk('public')->allFiles('resource'), "Failed asserting that no image files exist in the 'resource' directory after failed resource creation.");
    }

    public function test_model_removes_image_upon_deletion()
    {
        $resource = ComputerScienceResource::factory()->create();

        $imagePath = $resource->image_path;

        Storage::disk('public')->assertExists($imagePath);

        $resource->forceDelete();

        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_posting_duplicate_resource_returns_existing_resource()
    {
        $this->actingAs($this->user);

        // Create a resource first
        $formData = StoreResourceRequestFactory::new()->create();
        $this->postJson(route('resources.store'), $formData);

        // Try to create the same resource again
        $response = $this->postJson(route('resources.store'), $formData);

        $response->assertStatus(200);

        // Should return the existing resource, not create a new one
        $resources = ComputerScienceResource::where('name', $formData['name'])->get();
        $this->assertCount(1, $resources);
    }

    public function test_cleans_up_when_deleted()
    {
        $this->actingAs($this->user);

        Storage::fake('public');
        $resource = $this->createResource([
            'topics_tags' => ['test1'],
            'programming_languages_tags' => ['test2'],
            'general_tags' => ['test3'],
        ]);

        $commentData = $this->createComment('resource', $resource->id);
        $commentId = $commentData['id'];

        $resourceReview = $this->createReview($resource->id);

        // Add a fake image to the resource
        $imageFile = UploadedFile::fake()->image('test_image.jpg');
        $imagePath = 'resource/'.$imageFile->hashName();
        Storage::disk('public')->put($imagePath, $imageFile->getContent());
        $resource->image_path = $imagePath;
        $resource->save();

        // Assert resource, comment, and image exist before deletion
        $this->assertDatabaseHas('computer_science_resources', ['id' => $resource->id]);
        $this->assertDatabaseHas('comments', ['id' => $commentId]);
        Storage::disk('public')->assertExists($imagePath);

        $resource->forceDelete();

        // Assert resource and comment are deleted
        $this->assertDatabaseMissing('computer_science_resources', ['id' => $resource->id]);
        $this->assertDatabaseMissing('comments', ['id' => $commentId]);
        $this->assertDatabaseMissing('comments_counts', ['commentable_id' => $resource->id, 'commentable_type' => ComputerScienceResource::class]);

        // Assert Reviews are removed
        $this->assertDatabaseMissing('resource_reviews', ['id' => $resourceReview->id]);
        $this->assertDatabaseMissing('resource_review_summaries', ['computer_science_resource_id' => $resource->id]);

        // Assert tags are removed
        $this->assertNull(TagFrequency::where('type', 'topics_tags')->where('tag', 'test1')->first());
        $this->assertNull(TagFrequency::where('type', 'programming_languages_tags')->where('tag', 'test2')->first());
        $this->assertNull(TagFrequency::where('type', 'general_tags')->where('tag', 'test3')->first());

        // Assert image is deleted
        Storage::disk('public')->assertMissing($imagePath);
    }
}
