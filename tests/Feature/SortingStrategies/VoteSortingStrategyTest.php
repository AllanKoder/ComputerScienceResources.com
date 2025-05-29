<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\ResourceReview;
use App\Models\UpvoteSummary;
use App\Services\SortingManagers\GeneralVotesSortingManager;
use Carbon\Carbon;

class VoteSortingStrategyTest extends TestCase
{
    use RefreshDatabase;

    protected GeneralVotesSortingManager $sortingManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sortingManager = new GeneralVotesSortingManager();
    }

    public function test_it_sorts_resource_reviews_by_top_votes()
    {
        $reviews = ResourceReview::factory()->count(3)->create();

        // 10, 2, -1
        UpvoteSummary::factory()
            ->forUpvotable($reviews[0])
            ->create([ 'upvotes' => 10, 'downvotes' => 0 ]);
        UpvoteSummary::factory()
            ->forUpvotable($reviews[1])
            ->create([ 'upvotes' => 7, 'downvotes' => 8 ]); // 7 - 8 = -1
        UpvoteSummary::factory()
            ->forUpvotable($reviews[2])
            ->create([ 'upvotes' => 6, 'downvotes' => 4 ]);  // 6 - 4 = 2

        $sorted = $this->sortingManager
            ->applySort(ResourceReview::query(), 'top')
            ->pluck('id')
            ->toArray();

        // 10, 2, -1
        $this->assertEquals([
            $reviews[0]->id,
            $reviews[2]->id,
            $reviews[1]->id,
        ], $sorted);
    }

    public function test_it_sorts_resource_reviews_by_bottom_votes()
    {
        $reviews = ResourceReview::factory()->count(3)->create();

        // Scores: -5, 0, 5
        UpvoteSummary::factory()
            ->forUpvotable($reviews[0])
            ->create([ 'upvotes' => 0, 'downvotes' => 5 ]);
        UpvoteSummary::factory()
            ->forUpvotable($reviews[1])
            ->create([ 'upvotes' => 3, 'downvotes' => 3 ]);
        UpvoteSummary::factory()
            ->forUpvotable($reviews[2])
            ->create([ 'upvotes' => 10, 'downvotes' => 5 ]);

        $sorted = $this->sortingManager
            ->applySort(ResourceReview::query(), 'bottom')
            ->pluck('id')
            ->toArray();

        // -5, 0, 5
        $this->assertEquals([
            $reviews[0]->id,
            $reviews[1]->id,
            $reviews[2]->id,
        ], $sorted);
    }

    public function test_it_sorts_resource_reviews_by_controversial()
    {
        $reviews = ResourceReview::factory()->count(3)->create();

        // Controversy = total_votes - abs(diff)
        // For (up, down): (5, 5) -> 10 - 0 = 10
        //                (6, 4) -> 10 - 2 = 8
        //                (10,0) -> 10 - 10 = 0
        UpvoteSummary::factory()->forUpvotable($reviews[0])->create(['upvotes'=>5, 'downvotes'=>5]);
        UpvoteSummary::factory()->forUpvotable($reviews[1])->create(['upvotes'=>6, 'downvotes'=>4]);
        UpvoteSummary::factory()->forUpvotable($reviews[2])->create(['upvotes'=>10, 'downvotes'=>0]);

        $sorted = $this->sortingManager
            ->applySort(ResourceReview::query(), 'controversial')
            ->pluck('id')
            ->toArray();

        // ASC controversy: 10, 8, 0
        $this->assertEquals([
            $reviews[0]->id,
            $reviews[1]->id,
            $reviews[2]->id,
        ], $sorted);
    }

    public function test_it_sorts_resource_reviews_by_total_votes()
    {
        $reviews = ResourceReview::factory()->count(3)->create();

        // Total votes: 5, 10, 15
        UpvoteSummary::factory()->forUpvotable($reviews[0])->create(['upvotes'=>2, 'downvotes'=>3]);
        UpvoteSummary::factory()->forUpvotable($reviews[1])->create(['upvotes'=>5, 'downvotes'=>5]);
        UpvoteSummary::factory()->forUpvotable($reviews[2])->create(['upvotes'=>10, 'downvotes'=>5]);

        $sorted = $this->sortingManager
            ->applySort(ResourceReview::query(), 'total_votes')
            ->pluck('id')
            ->toArray();

        $this->assertEquals([
            $reviews[2]->id,
            $reviews[1]->id,
            $reviews[0]->id,
        ], $sorted);
    }

    public function test_it_sorts_resource_reviews_by_hot()
    {
        $now = Carbon::now();
        // review1: created 10h ago, score 100
        $review1 = ResourceReview::factory()->create(['created_at' => $now->copy()->subHours(10)]);
        // review2: created now, score 10
        $review2 = ResourceReview::factory()->create(['created_at' => $now]);

        UpvoteSummary::factory()->forUpvotable($review1)->create(['upvotes'=>100, 'downvotes'=>0]);
        UpvoteSummary::factory()->forUpvotable($review2)->create(['upvotes'=>10, 'downvotes'=>0]);

        $sorted = $this->sortingManager
            ->applySort(ResourceReview::query(), 'hot')
            ->pluck('id')
            ->toArray();

        // hot values: review2 > review1
        $this->assertEquals([
            $review2->id,
            $review1->id,
        ], $sorted);
    }
}
