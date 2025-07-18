<?php

namespace Tests\Feature;

use App\Models\ResourceReview;
use App\Models\UpvoteSummary;
use App\Services\SortingManagers\GeneralVotesSortingManager;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteSortingStrategyTest extends TestCase
{
    use RefreshDatabase;

    protected GeneralVotesSortingManager $sortingManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sortingManager = new GeneralVotesSortingManager;
    }

    public function test_it_sorts_resource_reviews_by_top_votes()
    {
        $reviews = ResourceReview::factory()->count(3)->create();

        // 10, 2, -1
        $summary1 = UpvoteSummary::where('upvotable_id', $reviews[0]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary1->upvotes = 10;
        $summary1->downvotes = 0;
        $summary1->save();

        $summary2 = UpvoteSummary::where('upvotable_id', $reviews[1]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary2->upvotes = 7;
        $summary2->downvotes = 8; // 7 - 8 = -1
        $summary2->save();

        $summary3 = UpvoteSummary::where('upvotable_id', $reviews[2]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary3->upvotes = 6;
        $summary3->downvotes = 4;  // 6 - 4 = 2
        $summary3->save();

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
        $summary1 = UpvoteSummary::where('upvotable_id', $reviews[0]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary1->upvotes = 0;
        $summary1->downvotes = 5;
        $summary1->save();

        $summary2 = UpvoteSummary::where('upvotable_id', $reviews[1]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary2->upvotes = 3;
        $summary2->downvotes = 3;
        $summary2->save();

        $summary3 = UpvoteSummary::where('upvotable_id', $reviews[2]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary3->upvotes = 10;
        $summary3->downvotes = 5;
        $summary3->save();

        $sorted = $this->sortingManager
            ->applySort(ResourceReview::query(), 'bottom')
            ->get()
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
        $summary1 = UpvoteSummary::where('upvotable_id', $reviews[0]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary1->upvotes = 5;
        $summary1->downvotes = 5;
        $summary1->save();

        $summary2 = UpvoteSummary::where('upvotable_id', $reviews[1]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary2->upvotes = 6;
        $summary2->downvotes = 4;
        $summary2->save();

        $summary3 = UpvoteSummary::where('upvotable_id', $reviews[2]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary3->upvotes = 10;
        $summary3->downvotes = 0;
        $summary3->save();

        $sorted = $this->sortingManager
            ->applySort(ResourceReview::query(), 'controversial')
            ->get()
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
        $summary1 = UpvoteSummary::where('upvotable_id', $reviews[0]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary1->upvotes = 2;
        $summary1->downvotes = 3;
        $summary1->save();

        $summary2 = UpvoteSummary::where('upvotable_id', $reviews[1]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary2->upvotes = 5;
        $summary2->downvotes = 5;
        $summary2->save();

        $summary3 = UpvoteSummary::where('upvotable_id', $reviews[2]->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary3->upvotes = 10;
        $summary3->downvotes = 5;
        $summary3->save();

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

        $summary1 = UpvoteSummary::where('upvotable_id', $review1->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary1->upvotes = 100;
        $summary1->downvotes = 0;
        $summary1->save();

        $summary2 = UpvoteSummary::where('upvotable_id', $review2->id)->where('upvotable_type', ResourceReview::class)->first();
        $summary2->upvotes = 10;
        $summary2->downvotes = 0;
        $summary2->save();

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
