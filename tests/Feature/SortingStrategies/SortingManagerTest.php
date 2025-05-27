<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use App\Models\User;
use App\Services\SortingManagers\ResourceSortingManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\TestResources\ComputerScienceResourceTestResource;

class SortingManagerTest extends TestCase
{
    use RefreshDatabase;

    protected ResourceSortingManager $sortingManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sortingManager = new ResourceSortingManager();

        $user = User::factory()->create();
        $this->actingAs($user);

        // Create 4 resources
        $resourceForm1 = ComputerScienceResourceTestResource::fake(['name'=>'name1']);
        $resourceForm2 = ComputerScienceResourceTestResource::fake(['name'=>'name2']);
        $resourceForm3 = ComputerScienceResourceTestResource::fake(['name'=>'name3']);
        $resourceForm4 = ComputerScienceResourceTestResource::fake(['name'=>'name4']);

        $response1 = $this->postJson(route('resources.store'), $resourceForm1);
        $response2 = $this->postJson(route('resources.store'), $resourceForm2);
        $response3 = $this->postJson(route('resources.store'), $resourceForm3);
        $response4 = $this->postJson(route('resources.store'), $resourceForm4);

        $response1->assertStatus(302);
        $response2->assertStatus(302);
        $response3->assertStatus(302);
        $response4->assertStatus(302);

        $resource1 = ComputerScienceResource::where('name', 'name1')->first();
        $resource2 = ComputerScienceResource::where('name', 'name2')->first();
        $resource3 = ComputerScienceResource::where('name', 'name3')->first();
        $resource4 = ComputerScienceResource::where('name', 'name4')->first();

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
        $resource1 = ComputerScienceResource::where('name', 'name1')->first();
        $resource2 = ComputerScienceResource::where('name', 'name2')->first();
        $resource3 = ComputerScienceResource::where('name', 'name3')->first();
        $resource4 = ComputerScienceResource::where('name', 'name4')->first();

        $sorted = $this->sortingManager
            ->applySort(ComputerScienceResource::query(), 'Fake Field')
            ->pluck('id')
            ->toArray();

        $this->assertEquals([
            $resource1->id,
            $resource2->id,
            $resource3->id,
            $resource4->id,
        ], $sorted);
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
