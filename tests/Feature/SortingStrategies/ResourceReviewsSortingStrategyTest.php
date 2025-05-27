<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Services\SortingManagers\ResourceSortingManager;
use App\Traits\HandlesResourceReviewJoins;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestResources\ResourceReviewTestResource;

class ResourceReviewsSortingStrategyTest extends TestCase
{
    use RefreshDatabase;
    use HandlesResourceReviewJoins;

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

        // Post reviews with increasing values for the target field
        $response1 = $this->postJson(
            route('reviews.store', $res1),
            ResourceReviewTestResource::fake([$field => 1])
        );
        $response2 = $this->postJson(
            route('reviews.store', $res2),
            ResourceReviewTestResource::fake([$field => 2])
        );
        $response3 = $this->postJson(
            route('reviews.store', $res3),
            ResourceReviewTestResource::fake([$field => 3])
        );
        $response4 = $this->postJson(
            route('reviews.store', $res4),
            ResourceReviewTestResource::fake([$field => 5])
        );
        $response5 = $this->postJson(
            route('reviews.store', $res5),
            ResourceReviewTestResource::fake([$field => 4])
        );

        // Assert all responses are successful
        $response1->assertStatus(200);
        $response2->assertStatus(200);
        $response3->assertStatus(200);
        $response4->assertStatus(200);
        $response5->assertStatus(200);

        // Sort by *_rating field (if generated column, still needs to be selected manually)
        $ratingField = "{$field}_rating";

        $sorted = $this->resourceSortingManager
            ->applySort(ComputerScienceResource::query(), $field)
            ->addSelect("resource_review_summaries.{$ratingField}")
            ->get()
            ->pluck('id')
            ->toArray();

        $this->assertEquals(
            [$res4->id, $res5->id, $res3->id, $res2->id, $res1->id],
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

        $this->postJson(route('reviews.store', $res1),
            ResourceReviewTestResource::fake(array_merge($defaultOnes, ['community' => 5, 'teaching_clarity' => 5]))
        )->assertStatus(200);

        $this->postJson(route('reviews.store', $res2),
            ResourceReviewTestResource::fake(array_merge($defaultOnes, ['community' => 5, 'teaching_clarity' => 2]))
        )->assertStatus(200);

        $this->postJson(route('reviews.store', $res3),
            ResourceReviewTestResource::fake(array_merge($defaultOnes, ['community' => 3, 'teaching_clarity' => 3]))
        )->assertStatus(200);

        $this->postJson(route('reviews.store', $res4),
            ResourceReviewTestResource::fake(array_merge($defaultOnes, ['community' => 2, 'teaching_clarity' => 2]))
        )->assertStatus(200);

        // Sort by overall
        $sorted = $this->resourceSortingManager
            ->applySort(ComputerScienceResource::query(), 'overall')
            ->addSelect('resource_review_summaries.overall_rating') // Optional depending on DB
            ->get()
            ->pluck('id')
            ->toArray();

        $this->assertEquals(
            [$res1->id, $res2->id, $res3->id, $res4->id],
            $sorted,
            "Failed asserting that resources are sorted by overall_rating"
        );
    }
}
