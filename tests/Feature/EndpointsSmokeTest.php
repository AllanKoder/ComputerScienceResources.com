<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class EndpointsSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test all unauthenticated GET endpoints.
     *
     * @return void
     */
    public function test_all_get_no_auth_endpoints()
    {
        // Unauthenticated GET endpoints
        $endpoints = [
            '/',
            '/resources',
            '/resource/1', // Assuming 1 is a valid ID
            '/comments/resource/1/', // Assuming 'type' and '1' are valid
            '/review/1', // Assuming 1 is a valid resource ID
            '/resource_edit/1', // Assuming 1 is a valid resource ID
            '/resource_edit/show/1', // Assuming 1 is a valid resource_edit ID
            '/resource_edit/original/1', // Assuming 1 is a valid resource_edit ID
            '/resource_edit/diff/1', // Assuming 1 is a valid resource_edit ID
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->get($endpoint);
            $this->assertTrue(
                $response->status() === 200,
                "Failed asserting that GET $endpoint returns status 200."
            );
        }
    }

    /**
     * Test all authenticated GET endpoints.
     *
     * @return void
     */
    public function test_all_get_auth_endpoints()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Authenticated GET endpoints
        $endpoints = [
            '/resources/create',
            '/resource_edit/1/create', // Assuming 1 is a valid resource ID
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->get($endpoint);
            $this->assertTrue(
                $response->status() === 200,
                "Failed asserting that GET $endpoint returns status 200."
            );
        }
    }
}
