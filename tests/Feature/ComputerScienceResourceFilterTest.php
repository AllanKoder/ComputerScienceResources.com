<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ComputerScienceResource;
use App\Services\ComputerScienceResourceFilter;
use App\Services\ResourceReviewService;
use App\Services\SortingManagers\ResourceSortingManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestResources\ComputerScienceResourceTestResource;
use Tests\Feature\Utils\ResourceUtils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ComputerScienceResourceFilterTest extends TestCase
{
    use RefreshDatabase, ResourceUtils;

    protected User $user;

    protected ComputerScienceResourceFilter $filterService;

    protected function setUp(): void
    {
        parent::setUp();

        // Instantiate dependencies
        $reviewService = app(ResourceReviewService::class);
        $resourceSortingManager = app(ResourceSortingManager::class);

        $this->filterService = new ComputerScienceResourceFilter($reviewService, $resourceSortingManager);

        foreach (range(1, 10) as $i) {
            $this->user = User::factory()->create();
            $this->actingAs($this->user);

            $resource = $this->createResource(['name' => "name{$i}"]);
            $this->createReview($resource->id, ['title' => "Review A {$i}"]);

            $this->user = User::factory()->create();
            $this->actingAs($this->user);
            $this->createReview($resource->id, ['title' => "Review B {$i}"]);
        }
    }

    public function test_can_get_resources()
    {
        $response = $this->get(route('resources.index'));
        $response->assertStatus(200);
        $response->assertSee('Resources');
    }

    public static function invalidFieldProvider(): array
    {
        return [
            'name too long' => ['name', str_repeat('a', 1001)],
            'description too long' => ['description', str_repeat('a', 1001)],
            'platforms not array' => ['platforms', 'not-an-array'],
            'platforms item invalid' => ['platforms', ['invalid_platform']],
            'platforms not distinct' => ['platforms', ['web', 'web']],
            'difficulty invalid' => ['difficulty', 'super-hard'],
            'pricing invalid' => ['pricing', 'expensive'],
            'topics too few' => ['topics', ['a', 'b']],
            'topics item too long' => ['topics', ['a', str_repeat('b', 51), 'c']],
            'topics not distinct' => ['topics', ['a', 'a', 'a']],
            'general_tags not array' => ['general_tags', 'not-an-array'],
            'general_tags item too long' => ['general_tags', [str_repeat('a', 51)]],
            'general_tags not distinct' => ['general_tags', ['x', 'x']],
            'programming_languages not array' => ['programming_languages', 'not-an-array'],
            'programming_languages item too long' => ['programming_languages', [str_repeat('a', 51)]],
            'programming_languages not distinct' => ['programming_languages', ['js', 'js']],
            'community_rating too low' => ['community_rating', 0],
            'community_rating too high' => ['community_rating', 5],
            'teaching_clarity not integer' => ['teaching_clarity', 'high'],
            'engagement invalid' => ['engagement', 100],
            'practicality invalid' => ['practicality', -1],
            'user_friendliness invalid' => ['user_friendliness', 999],
            'updates not integer' => ['updates', 'often'],
            'updates too high' => ['updates', 6],
            'created_from not a date' => ['created_from', 'not-a-date'],
            'created_to not a date' => ['created_to', '2023-02-30'],
            'updated_from not a date' => ['updated_from', 'not-a-date'],
            'updated_to not a date' => ['updated_to', 'yesterday'],
            'sort_by not a string' => ['sort_by', ['array']],
            'reverse not a string' => ['reverse', ['true']],
        ];
    }

    #[DataProvider('invalidFieldProvider')]
    #[Test]
    #[Group('slow')]
    public function test_cannot_filter_with_invalid_fields(string $field, mixed $invalidValue)
    {
        $this->actingAs($this->user);

        $validData = ComputerScienceResourceTestResource::fake();
        $validData[$field] = $invalidValue;

        $response = $this->getJson(route('resources.index', $validData));
        $response->assertStatus(422);
    }
}
