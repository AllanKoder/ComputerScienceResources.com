<?php

namespace Tests\Feature;

use App\Models\ComputerScienceResource;
use App\Models\User;
use App\Services\ModelResolverService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpvoteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test upvote can only be done on a valid upvotable type and id.
     */
    public function test_upvote_on_invalid_type_returns_422()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('upvote', ['type' => 'invalid_type', 'id' => 1]));
        $response->assertStatus(422);
    }

    /**
     * Test upvote can only be done on an existing upvotable type and id.
     */
    public function test_upvote_on_non_existing_type_returns_422()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('upvote', ['type' => 'resource', 'id' => 1]));
        $response->assertStatus(404);
    }

    /**
     * Test that upvoting works on all upvotable types defined in config.
     */
    public function test_can_upvote_all_upvotable_types()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        foreach (config('upvotes.upvotable_types') as $typeKey) {
            // Resolve the model class.
            $modelClass = app(ModelResolverService::class)->getModelClass($typeKey);

            $model = $modelClass::factory()->create();

            $response = $this->postJson(route('upvote', [
                'type' => $typeKey,
                'id' => $model->id,
            ]));

            $response->assertStatus(200);
            $this->assertDatabaseHas('upvotes', [
                'user_id' => $user->id,
                'upvotable_type' => $modelClass,
                'upvotable_id' => $model->id,
                'value' => 1,
            ]);
        }
    }

    /**
     * Test that a user must be logged in to upvote.
     */
    public function test_upvote_requires_authenticated_user()
    {
        $response = $this->postJson(route('upvote', ['type' => 'resource', 'id' => 1]));
        $response->assertStatus(401); // Unauthorized
    }

    /**
     * Test upvote increments the upvote count.
     */
    public function test_upvote_increases_upvote_count()
    {
        $user = User::factory()->create();
        $resource = ComputerScienceResource::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('upvote', ['type' => 'resource', 'id' => $resource->id]));
        $response->assertStatus(200);

        // Check the upvote summary or actual resource to confirm the upvote
        $this->assertEquals(1, $resource->upvoteSummary->upvotes);
    }

    /**
     * Test downvote decreases the upvote count.
     */
    public function test_downvote_decreases_upvote_count()
    {
        $user = User::factory()->create();
        $resource = ComputerScienceResource::factory()->create();
        $this->actingAs($user);

        // First, upvote
        $this->postJson(route('upvote', ['type' => 'resource', 'id' => $resource->id]));

        // Now, downvote
        $response = $this->postJson(route('downvote', ['type' => 'resource', 'id' => $resource->id]));
        $response->assertStatus(200);

        // Check the upvote summary or actual resource to confirm downvote
        $this->assertEquals(0, $resource->upvoteSummary->upvotes);
        $this->assertEquals(1, $resource->upvoteSummary->downvotes);
    }

    /**
     * Test multiple downvotes from different users results in negative votes.
     */
    public function test_multiple_downvotes()
    {
        $users = User::factory(3)->create();
        $resource = ComputerScienceResource::factory()->create();

        // Downvote by 3 users
        foreach ($users as $user) {
            $this->actingAs($user);
            $this->postJson(route('downvote', ['type' => 'resource', 'id' => $resource->id]));
        }

        // Check if the downvotes have been accumulated correctly
        $this->assertEquals(0, $resource->upvoteSummary->upvotes); // No upvotes
        $this->assertEquals(3, $resource->upvoteSummary->downvotes); // 3 downvotes
    }

    /**
     * Test multiple upvotes from different users results in positive votes.
     */
    public function test_multiple_upvotes()
    {
        $users = User::factory(3)->create();
        $resource = ComputerScienceResource::factory()->create();

        // Upvote by 3 users
        foreach ($users as $user) {
            $this->actingAs($user);
            $this->postJson(route('upvote', ['type' => 'resource', 'id' => $resource->id]));
        }

        // Check if the upvotes have been accumulated correctly
        $this->assertEquals(3, $resource->upvoteSummary->upvotes); // 3 upvotes
        $this->assertEquals(0, $resource->upvoteSummary->downvotes); // No downvotes
    }

    /**
     * Test upvote after downvote makes the score 0.
     */
    public function test_upvote_after_downvote_resets_to_zero()
    {
        $user = User::factory()->create();
        $resource = ComputerScienceResource::factory()->create();
        $this->actingAs($user);

        // First downvote
        $this->postJson(route('downvote', ['type' => 'resource', 'id' => $resource->id]));

        // Now upvote
        $this->postJson(route('upvote', ['type' => 'resource', 'id' => $resource->id]));

        // Check that the upvotes is now 1
        $this->assertEquals(1, $resource->upvoteSummary->upvotes);
        // Check that the downvotes is reset to 0 after upvote
        $this->assertEquals(0, $resource->upvoteSummary->downvotes);
    }

    /**
     * Test downvote after upvote makes the score 0.
     */
    public function test_downvote_after_upvote_resets_to_zero()
    {
        $user = User::factory()->create();
        $resource = ComputerScienceResource::factory()->create();
        $this->actingAs($user);

        // First upvote
        $this->postJson(route('upvote', ['type' => 'resource', 'id' => $resource->id]));

        // Now downvote
        $this->postJson(route('downvote', ['type' => 'resource', 'id' => $resource->id]));

        // Check that the upvotes is reset to 0 after upvote
        $this->assertEquals(0, $resource->upvoteSummary->upvotes);
        // Check that the downvotes is now 1
        $this->assertEquals(1, $resource->upvoteSummary->downvotes);
    }

    /**
     * Test upvote and downvote sum to zero.
     */
    public function test_upvote_and_downvote_sum_to_zero()
    {
        $users1 = User::factory(3)->create();
        $resource = ComputerScienceResource::factory()->create();

        // Upvote by 3 users
        foreach ($users1 as $user) {
            $this->actingAs($user);
            $this->postJson(route('upvote', ['type' => 'resource', 'id' => $resource->id]));
        }

        $users2 = User::factory(3)->create();
        // Downvote by 3 users
        foreach ($users2 as $user) {
            $this->actingAs($user);
            $this->postJson(route('downvote', ['type' => 'resource', 'id' => $resource->id]));
        }

        // Check if the final score is zero
        $this->assertEquals(3, $resource->upvoteSummary->upvotes);
        $this->assertEquals(3, $resource->upvoteSummary->downvotes);
        $this->assertEquals(0, $resource->upvoteSummary->voteScore);
    }
}
