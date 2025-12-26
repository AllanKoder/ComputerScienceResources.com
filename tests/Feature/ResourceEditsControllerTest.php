<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceEditsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test that the resource edits index page renders successfully
     */
    public function test_index_page_renders(): void
    {
        $response = $this->get(route('resource_edits.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('ResourceEdits/Index')
            ->has('resource_edits')
            ->has('sortingType')
        );
    }

    /**
     * Test that the index page displays resource edits
     */
    public function test_index_page_displays_resource_edits(): void
    {
        $resource = ComputerScienceResource::factory()->create();

        ResourceEdits::factory()->count(3)->create([
            'computer_science_resource_id' => $resource->id,
        ]);

        $response = $this->get(route('resource_edits.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('resource_edits.data', 3)
        );
    }

    /**
     * Test that the create page renders successfully
     */
    public function test_create_page_renders(): void
    {
        $this->actingAs($this->user);

        $resource = ComputerScienceResource::factory()->create();

        $response = $this->get(route('resource_edits.create', ['slug' => $resource->slug]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('ResourceEdits/Create')
            ->has('resource')
        );
    }

    /**
     * Test that the show page renders successfully
     */
    public function test_show_page_renders(): void
    {
        $resource = ComputerScienceResource::factory()->create();
        $edit = ResourceEdits::factory()->create([
            'computer_science_resource_id' => $resource->id,
        ]);

        $response = $this->get(route('resource_edits.show', ['slug' => $edit->slug]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('ResourceEdits/Show')
            ->has('editedResource')
        );
    }
}
