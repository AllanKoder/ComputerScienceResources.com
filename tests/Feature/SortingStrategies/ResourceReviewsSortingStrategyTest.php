<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Services\SortingManagers\ResourceSortingManager;
use App\Traits\HandlesResourceReviewJoins;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Utils\ResourceUtils;
use Tests\TestResources\ResourceReviewTestResource;

class ResourceReviewsSortingStrategyTest extends TestCase
{
    use RefreshDatabase;
    use HandlesResourceReviewJoins;
    use ResourceUtils;

    protected ResourceSortingManager $resourceSortingManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resourceSortingManager = new ResourceSortingManager();
    }

    public static function reviewFieldsProvider(): array
    {
        return [
            ['community'],
            ['teaching_clarity'],
            ['engagement'],
            ['practicality'],
            ['user_friendliness'],
            ['updates'],
        ];
    }

    #[DataProvider('reviewFieldsProvider')]
    public function test_it_sorts_summaries_by_field(string $field): void
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create 3 resources
        $res1 = ComputerScienceResource::factory()->create();
        $res2 = ComputerScienceResource::factory()->create();
        $res3 = ComputerScienceResource::factory()->create();
        $res4 = ComputerScienceResource::factory()->create();
        $res5 = ComputerScienceResource::factory()->create();

        $this->createReview($res1->id, array_merge([$field => 1]));
        $this->createReview($res2->id, array_merge([$field => 2]));
        $this->createReview($res3->id, array_merge([$field => 3]));
        $this->createReview($res4->id, array_merge([$field => 4]));
        $this->createReview($res5->id, array_merge([$field => 5]));

        // Sort by *_rating field (if generated column, still needs to be selected manually)
        $ratingField = "{$field}_rating";

        $sorted = $this->resourceSortingManager
            ->applySort(ComputerScienceResource::query(), $field)
            ->addSelect("resource_review_summaries.{$ratingField}")
            ->get()
            ->pluck('id')
            ->toArray();

        $this->assertEquals(
            [$res5->id, $res4->id, $res3->id, $res2->id, $res1->id],
            $sorted,
            "Failed asserting that resources are sorted by {$field}"
        );
    }

    public function test_it_sorts_by_overall_rating_with_multiple_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create resources
        $res1 = ComputerScienceResource::factory()->create(); // 5 community, 5 teaching_clarity = 5 avg
        $res2 = ComputerScienceResource::factory()->create(); // 5 community, 2 teaching_clarity = 3.5 avg
        $res3 = ComputerScienceResource::factory()->create(); // 3 community, 3 teaching_clarity = 3 avg
        $res4 = ComputerScienceResource::factory()->create(); // 2 community, 2 teaching_clarity = 2 avg

        $defaultOnes = [
            'community' => 1,
            'teaching_clarity' => 1,
            'engagement' => 1,
            'practicality' => 1,
            'user_friendliness' => 1,
            'updates' => 1,
        ];

        $this->createReview($res1->id, array_merge($defaultOnes, ['community' => 5, 'teaching_clarity' => 5]));
        $this->createReview($res2->id, array_merge($defaultOnes, ['community' => 5, 'teaching_clarity' => 2]));
        $this->createReview($res3->id, array_merge($defaultOnes, ['community' => 3, 'teaching_clarity' => 3]));
        $this->createReview($res4->id, array_merge($defaultOnes, ['community' => 2, 'teaching_clarity' => 2]));

        // Sort by overall
        $sorted = $this->resourceSortingManager
            ->applySort(ComputerScienceResource::query(), 'overall')
            ->addSelect('resource_review_summaries.overall_rating')
            ->get()
            ->pluck('id')
            ->toArray();

        $this->assertEquals(
            [$res1->id, $res2->id, $res3->id, $res4->id],
            $sorted,
            "Failed asserting that resources are sorted by overall_rating"
        );
    }

    public function test_it_sorts_by_overall_rating_with_multiple_votes(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create resources
        $res1 = computerscienceresource::factory()->create(); // 12 community, 3 reviews = 4 overall
        $res2 = ComputerScienceResource::factory()->create(); // 10 community, 2 reviews = 5 overall
        $res3 = ComputerScienceResource::factory()->create(); // 7 community, 2 reviews = 3.5 overall
        $res4 = ComputerScienceResource::factory()->create(); // 2 community, 1 reviews = 2 overall

        $defaultOnes = [
            'community' => -1,
            'teaching_clarity' => 1,
            'engagement' => 1,
            'practicality' => 1,
            'user_friendliness' => 1,
            'updates' => 1,
        ];

        $this->createReview($res1->id, array_merge($defaultOnes, ['community' => 5]), true);
        $this->createReview($res1->id, array_merge($defaultOnes, ['community' => 5]), true);
        $this->createReview($res1->id, array_merge($defaultOnes, ['community' => 3]), true);

        $this->createReview($res2->id, array_merge($defaultOnes, ['community' => 5]), true);
        $this->createReview($res2->id, array_merge($defaultOnes, ['community' => 5]), true);

        $this->createReview($res3->id, array_merge($defaultOnes, ['community' => 4]), true);
        $this->createReview($res3->id, array_merge($defaultOnes, ['community' => 3]), true);

        $this->createReview($res4->id, array_merge($defaultOnes, ['community' => 2]), true);

        // Sort by overall
        $sorted = $this->resourceSortingManager
            ->applySort(ComputerScienceResource::query(), 'overall')
            ->addSelect('resource_review_summaries.overall_rating')
            ->get()
            ->pluck('id')
            ->toArray();

        $this->assertEquals(
            [$res2->id, $res1->id, $res3->id, $res4->id],
            $sorted,
            "Failed asserting that resources are sorted by overall_rating and review count"
        );
    }
}
