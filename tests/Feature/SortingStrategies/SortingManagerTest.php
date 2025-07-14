<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use App\Models\User;
use App\Services\SortingManagers\ResourceSortingManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\Utils\TestingUtils;
use Tests\TestResources\ComputerScienceResourceTestResource;

class SortingManagerTest extends TestCase
{
    use RefreshDatabase;
    use TestingUtils;

    protected ResourceSortingManager $sortingManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sortingManager = new ResourceSortingManager();

        $user = User::factory()->create();
        $this->actingAs($user);

        // Create 4 resources
        $resource1 = $this->createResource(['name' => 'name1']);
        $resource2 = $this->createResource(['name' => 'name2']);
        $resource3 = $this->createResource(['name' => 'name3']);
        $resource4 = $this->createResource(['name' => 'name4']);

        # Set the resource's dates in descending order
        $resource1->created_at = "2025-05-25 18:17:19";
        $resource2->created_at = "2025-05-25 18:17:18";
        $resource3->created_at = "2024-05-25 18:17:18";
        $resource4->created_at = "2024-05-24 18:17:18";

        $resource1->updated_at = "2025-05-25 18:17:19";
        $resource2->updated_at = "2025-05-25 18:17:18";
        $resource3->updated_at = "2024-05-25 18:17:18";
        $resource4->updated_at = "2024-05-24 18:17:18";

        $resource1->save();
        $resource2->save();
        $resource3->save();
        $resource4->save();
    }

    public function test_can_sort_by_date()
    {
        $resource1 = ComputerScienceResource::where('name', 'name1')->first();
        $resource2 = ComputerScienceResource::where('name', 'name2')->first();
        $resource3 = ComputerScienceResource::where('name', 'name3')->first();
        $resource4 = ComputerScienceResource::where('name', 'name4')->first();

        $sorted = $this->sortingManager
            ->applySort(ComputerScienceResource::query(), 'latest')
            ->pluck('id')
            ->toArray();

        $this->assertEquals([
            $resource1->id,
            $resource2->id,
            $resource3->id,
            $resource4->id,
        ], $sorted);
    }

    public function test_sorting_manager_can_reverse()
    {
        $resource1 = ComputerScienceResource::where('name', 'name1')->first();
        $resource2 = ComputerScienceResource::where('name', 'name2')->first();
        $resource3 = ComputerScienceResource::where('name', 'name3')->first();
        $resource4 = ComputerScienceResource::where('name', 'name4')->first();

        $sorted = $this->sortingManager
            ->applySort(ComputerScienceResource::query(), 'latest');
        $sorted = $this->sortingManager->reverse($sorted)
            ->pluck('id')
            ->toArray();

        $this->assertEquals([
            $resource4->id,
            $resource3->id,
            $resource2->id,
            $resource1->id,
        ], $sorted);
    }

    public function test_sorting_manager_denies_fake_sort_by_field()
    {
        // Query with explicit orderBy id ASC before applying sort
        $query = ComputerScienceResource::orderBy('id');

        $defaultOrder = $query->pluck('id')->toArray();

        $sorted = $this->sortingManager
            ->applySort($query, 'Fake Field') // applySort should NOT modify query because field unsupported
            ->pluck('id')
            ->toArray();

        $this->assertEquals(
            $defaultOrder,
            $sorted,
            "SortingManager should not modify order when sorting by invalid field."
        );
    }

    public function test_can_sort_by_date_reverse()
    {
        $resource1 = ComputerScienceResource::where('name', 'name1')->first();
        $resource2 = ComputerScienceResource::where('name', 'name2')->first();
        $resource3 = ComputerScienceResource::where('name', 'name3')->first();
        $resource4 = ComputerScienceResource::where('name', 'name4')->first();

        $reverseSorted = $this->sortingManager
            ->applySort(ComputerScienceResource::query(), 'oldest')
            ->pluck('id')
            ->toArray();
        $this->assertEquals([
            $resource4->id,
            $resource3->id,
            $resource2->id,
            $resource1->id,
        ], $reverseSorted);
    }


    public function test_can_sort_by_latest_changes()
    {
        $resource1 = ComputerScienceResource::where('name', 'name1')->first();
        $resource2 = ComputerScienceResource::where('name', 'name2')->first();
        $resource3 = ComputerScienceResource::where('name', 'name3')->first();
        $resource4 = ComputerScienceResource::where('name', 'name4')->first();

        $reverseSorted = $this->sortingManager
            ->applySort(ComputerScienceResource::query(), 'recently_updated')
            ->pluck('id')
            ->toArray();
        $this->assertEquals([
            $resource1->id,
            $resource2->id,
            $resource3->id,
            $resource4->id,
        ], $reverseSorted);
    }
}
