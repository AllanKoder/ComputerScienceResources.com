<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use App\Models\User;
use App\Services\ComputerScienceResourceFilter;
use App\Services\ResourceReviewService;
use App\Services\SortingManagers\ResourceSortingManager;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Utils\TestingUtils;
use Tests\RequestFactories\StoreResourceRequestFactory;
use Tests\TestCase;
use Throwable;

class ComputerScienceResourceFilterTest extends TestCase
{
    use RefreshDatabase, TestingUtils;

    protected User $user;

    protected ComputerScienceResourceFilter $filterService;

    protected function setUp(): void
    {
        parent::setUp();

        // Instantiate dependencies
        $reviewService = app(ResourceReviewService::class);
        $resourceSortingManager = app(ResourceSortingManager::class);

        $this->filterService = new ComputerScienceResourceFilter($reviewService, $resourceSortingManager);
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
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
            'difficulty invalid' => ['difficulties', ['super-hard']],
            'pricing invalid' => ['pricing', 'expensive'],
            'topics item too long' => ['topics_tags', ['a', str_repeat('b', 51), 'c']],
            'topics not distinct' => ['topics_tags', ['a', 'a', 'a']],
            'general_tags not array' => ['general_tags', 'not-an-array'],
            'general_tags item too long' => ['general_tags', [str_repeat('a', 51)]],
            'general_tags not distinct' => ['general_tags', ['x', 'x']],
            'programming_languages not array' => ['programming_languages_tags', 'not-an-array'],
            'programming_languages item too long' => ['programming_languages_tags', [str_repeat('a', 51)]],
            'programming_languages not distinct' => ['programming_languages_tags', ['js', 'js']],
            'community too low' => ['community', 0],
            'community too high' => ['community', 5],
            'teaching_clarity not integer' => ['teaching_clarity', 'high'],
            'engagement invalid' => ['engagement', 100],
            'practicality invalid' => ['practicality', -1],
            'user_friendliness invalid' => ['user_friendliness', 999],
            'updates not integer' => ['updates', 'often'],
            'updates too high' => ['updates', 6],
            'overall too low' => ['overall', 0],
            'overall too high' => ['overall', 5],
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
        $validData = StoreResourceRequestFactory::new()->create();
        $validData[$field] = $invalidValue;

        $response = $this->getJson(route('resources.index', $validData));
        $response->assertStatus(422);
    }

    public static function filterProvider(): array
    {
        return [
            'by name' => [['name' => 'Graph Theory']],

            'by description' => [['description' => 'unit testing best practices']],

            'by platforms' => [['platforms' => ['website', 'bootcamp']]],

            'by difficulty & pricing' => [[
                'difficulties' => ['advanced'],
                'pricing' => ['free'],
            ]],

            'by topics, languages & general tags' => [[
                'topics_tags' => ['algorithms'],
                'programming_languages_tags' => ['php', 'javascript'],
                'general_tags' => ['tutorial', 'lecture'],
            ]],

            'by ratings' => [[
                'community' => 4,
                'teaching_clarity' => 3,
                'engagement' => 2,
                'practicality' => 4,
                'user_friendliness' => 3,
                'updates' => 4,
                'overall' => 2,
            ]],

            'by created & updated dates' => [[
                'created_from' => '2025-01-01',
                'created_to' => '2025-03-01',
                'updated_from' => '2025-02-01',
                'updated_to' => '2025-04-01',
            ]],

            'sorted newest reversed' => [[
                'sort_by' => 'latest',
                'reverse' => 'true',
            ]],

            'all filters together' => [[
                'name' => 'Graph',
                'description' => 'algorithm analysis',
                'platforms' => ['podcast', 'website'],
                'difficulties' => ['introduction'],
                'pricing' => ['free'],
                'topics_tags' => ['algorithms', 'recursion', 'data-structures'],
                'programming_languages_tags' => ['python'],
                'general_tags' => ['interactive', 'educational', 'advanced'],
                'community' => 4,
                'teaching_clarity' => 4,
                'overall' => 4,
                'created_from' => '2025-01-15',
                'sort_by' => 'top',
                'reverse' => 'false',
            ]],
        ];
    }

    public function interpolateQuery(string $sql, array $bindings): string
    {
        foreach ($bindings as $binding) {
            // Quote strings
            $binding = is_numeric($binding) ? $binding : "'$binding'";
            $sql = preg_replace('/\?/', $binding, $sql, 1);
        }

        return $sql;
    }

    #[DataProvider('filterProvider')]
    public function test_apply_filters(array $filters)
    {
        $query = ComputerScienceResource::query();
        $filtered = $this->filterService->applyFilters($query, $filters);

        $sql = $filtered->toSql();
        $bindings = $filtered->getBindings();
        $interpolated = $this->interpolateQuery($sql, $bindings);

        $this->assertInstanceOf(Builder::class, $filtered);

        // Full-text search
        if (! empty($filters['name'])) {
            $this->assertStringContainsStringIgnoringCase('MATCH (`name`)', $sql);
            $this->assertContains($filters['name'], $bindings);
        }

        if (! empty($filters['description'])) {
            $this->assertStringContainsStringIgnoringCase('MATCH (`description`)', $sql);
            $this->assertContains($filters['description'], $bindings);
        }

        // Platforms (FIND_IN_SET)
        if (! empty($filters['platforms'])) {
            foreach ($filters['platforms'] as $platform) {
                $this->assertStringContainsStringIgnoringCase('FIND_IN_SET', $sql);
                $this->assertContains($platform, $bindings);
            }
        }

        // Difficulty
        if (! empty($filters['difficulties'])) {
            foreach ($filters['difficulties'] as $difficulty) {
                $this->assertContains($difficulty, $bindings);
            }
        }

        // Pricing
        if (! empty($filters['pricing'])) {
            foreach ($filters['pricing'] as $pricing) {
                $this->assertContains($pricing, $bindings);
            }
        }

        // Tags (just check joins exist)
        if (! empty($filters['topics_tags'])) {
            $this->assertStringContainsString('taggables', $sql); // indirect check
        }
        if (! empty($filters['programming_languages_tags'])) {
            $this->assertStringContainsString('taggables', $sql); // indirect check
        }
        if (! empty($filters['general_tags'])) {
            $this->assertStringContainsString('taggables', $sql); // indirect check
        }

        // Ratings
        $ratingFields = [
            'community',
            'teaching_clarity',
            'engagement',
            'practicality',
            'user_friendliness',
            'updates',
            'overall',
        ];

        foreach ($ratingFields as $field) {
            if (! empty($filters[$field])) {
                // Assert the rating value was created
                $value = $filters[$field];
                $this->assertStringContainsString("`{$field}_rating` >= $value", $interpolated);
            }
        }

        // Date filters
        if (! empty($filters['created_from'])) {
            $this->assertStringContainsString("date(`computer_science_resources`.`created_at`) >= '{$filters['created_from']}'", $interpolated);
        }

        if (! empty($filters['created_to'])) {
            $this->assertStringContainsString("date(`computer_science_resources`.`created_at`) <= '{$filters['created_to']}'", $interpolated);
        }

        if (! empty($filters['updated_from'])) {
            $this->assertStringContainsString("date(`computer_science_resources`.`updated_at`) >= '{$filters['updated_from']}'", $interpolated);
        }

        if (! empty($filters['updated_to'])) {
            $this->assertStringContainsString("date(`computer_science_resources`.`updated_at`) <= '{$filters['updated_to']}'", $interpolated);
        }

        // Sorting shows the orderby, covered in sorting strategies tests.
        if (! empty($filters['sort_by'])) {
            $this->assertNotNull($filtered->getQuery()->orders);
        }

        if (! empty($filters['reverse']) && $filters['reverse'] === 'true') {
            // reverse() changes order direction
            $orderClause = $filtered->getQuery()->orders[0] ?? [];
            // Actual reverse test is covered in sorting manager test SortingStrategies/SortingManagerTest.php
            $this->assertEqualsIgnoringCase('asc', strtolower($orderClause['direction']));
        }

        try {
            $filtered->get();
            $this->assertTrue(true); // If we get here, the query is valid
        } catch (Throwable $e) {
            $this->fail('Query failed: '.$e->getMessage());
        }
    }
}
