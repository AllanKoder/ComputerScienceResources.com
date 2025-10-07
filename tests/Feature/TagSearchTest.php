<?php

namespace Tests\Feature;

use App\Events\TagFrequencyChanged;
use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\TagFrequency;
use App\Models\User;
use App\Services\ResourceEditsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\Feature\Utils\TestingUtils;
use Tests\RequestFactories\StoreResourceRequestFactory;
use Tests\TestCase;

class TagSearchTest extends TestCase
{
    use RefreshDatabase;
    use TestingUtils;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_search_tags_by_prefix()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->createResource(['general_tags' => ['python', 'pygame', 'java']]);
        $this->createResource(['general_tags' => ['python', 'pygame']]);
        $this->createResource(['general_tags' => ['python']]);

        $response = $this->getJson(route('tags.search', ['type' => 'general_tags', 'query' => 'py']));

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'tags'); // only 'python' and 'pygame'

        $response->assertJsonFragment(['tag' => 'python', 'count' => 3]);
        $response->assertJsonFragment(['tag' => 'pygame', 'count' => 2]);

        // Ensure order by count descending
        $tags = collect($response->json('tags'));
        $this->assertEquals(['python', 'pygame'], $tags->pluck('tag')->toArray());
    }

    public function test_query_too_long_returns_422()
    {
        $query = str_repeat('a', 51); // too long

        $response = $this->getJson(route('tags.search', ['type' => 'general_tags', 'query' => $query]));
        $response->assertStatus(422);
    }

    public function test_query_bad_type_returns_422()
    {
        $response = $this->getJson(route('tags.search', ['type' => 'bad_type']));
        $response->assertStatus(422);
    }

    public function test_creating_resource_updates_tag_frequency()
    {
        $this->actingAs($this->user);

        $formData = StoreResourceRequestFactory::new()->create([
            'topics_tags' => ['python', 'algorithms', 'java'],
            'programming_languages_tags' => ['python'],
            'general_tags' => ['beginner'],
        ]);

        $response = $this->postJson(route('resources.store'), $formData);

        $response->assertStatus(200);

        // Check that TagFrequency reflects counts
        $this->assertDatabaseHas('tag_frequencies', ['tag' => 'python', 'type' => 'topics_tags', 'count' => 1]);
        $this->assertDatabaseHas('tag_frequencies', ['tag' => 'python', 'type' => 'programming_languages_tags', 'count' => 1]);
        $this->assertDatabaseHas('tag_frequencies', ['tag' => 'algorithms', 'type' => 'topics_tags', 'count' => 1]);
        $this->assertDatabaseHas('tag_frequencies', ['tag' => 'beginner', 'type' => 'general_tags', 'count' => 1]);
    }

    public function test_dispatching_tag_frequency_change_removes_unused_tags()
    {
        // Step 1: Add initial tags via dispatch
        TagFrequencyChanged::dispatch('general_tags', [], [
            'python',
            'java',
            'ruby',
        ]);

        $this->assertDatabaseHas('tag_frequencies', ['tag' => 'python', 'count' => 1]);
        $this->assertDatabaseHas('tag_frequencies', ['tag' => 'java', 'count' => 1]);
        $this->assertDatabaseHas('tag_frequencies', ['tag' => 'ruby', 'count' => 1]);

        // Step 2: Dispatch with zero counts to simulate removal
        TagFrequencyChanged::dispatch('general_tags', [
            'python',
            'java',
            'ruby',
        ], []); // no tags used now

        // Step 3: Ensure all tag frequencies are removed
        $this->assertDatabaseMissing('tag_frequencies', ['tag' => 'python']);
        $this->assertDatabaseMissing('tag_frequencies', ['tag' => 'java']);
        $this->assertDatabaseMissing('tag_frequencies', ['tag' => 'ruby']);

        $response = $this->getJson(route('tags.search', ['type' => 'general_tags', 'query' => 'py']));
        $response->assertStatus(200);
        $this->assertEmpty($response->json('tags'));
    }

    public function test_merging_edit_correctly_updates_tag_frequency()
    {
        $resource = ComputerScienceResource::factory()
            ->setTags(
                ['algorithms', 'tag1', 'tag2'],
                ['c++'],
                ['reference']
            )
            ->create();

        $this->actingAs($this->user);

        // Create an edit with new tags
        $editData['edit_title'] = 'Tag Update';
        $editData['edit_description'] = 'Tag change for test';
        $editData['proposed_changes'] = [];
        $editData['proposed_changes']['topics_tags'] = ['python', 'algorithms', 'tag1'];
        $editData['proposed_changes']['programming_languages_tags'] = ['python'];
        $editData['proposed_changes']['general_tags'] = ['tutorial'];

        // Create the edit
        $response = $this->post(route('resource_edits.store', ['computerScienceResource' => $resource->id]), $editData);
        $response->assertStatus(302);

        $edit = ResourceEdits::latest()->first();

        // Mock approval
        $this->instance(ResourceEditsService::class, Mockery::mock(ResourceEditsService::class, function ($mock) {
            $mock->shouldReceive('canMergeEdits')->andReturnTrue();
        }));

        // Merge the edit
        $mergeResponse = $this->post(route('resource_edits.merge', ['resourceEdits' => $edit->id]));
        $mergeResponse->assertStatus(302);

        // TagFrequency should now reflect the changes
        $this->assertEquals(1, TagFrequency::where('tag', 'python')->where('type', 'topics_tags')->value('count'));
        $this->assertEquals(1, TagFrequency::where('tag', 'python')->where('type', 'programming_languages_tags')->value('count'));
        $this->assertEquals(1, TagFrequency::where('tag', 'algorithms')->value('count')); // Still used once
        $this->assertDatabaseMissing('tag_frequencies', ['tag' => 'c++']); // Removed
        $this->assertEquals(1, TagFrequency::where('tag', 'tutorial')->value('count'));
    }
}
