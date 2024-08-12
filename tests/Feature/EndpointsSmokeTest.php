<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\UserSeeder;
use Database\Seeders\ResourceSeeder;
use Database\Seeders\ResourceReviewSeeder;
use Database\Seeders\CommentSeeder;
use App\Models\User;
class EndpointsSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    
        // Debugging: Check if users are seeded
        $this->assertTrue(User::count() > 0, 'No users found in the database.');
    
        $this->seed(ResourceSeeder::class);
        $this->seed(ResourceReviewSeeder::class);
        $this->seed(CommentSeeder::class);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_all_get_no_auth_endpoints()
    {
        // get endpoints that don't need auth
        $endpoints = [
            '/',
            '/resources',
            '/resource/1', // Assuming 1 is a valid ID
            '/resource/1/comments', // Assuming 'type' and '1' are valid
            '/review/1', // Assuming 1 is a valid resource ID
            // '/resource_edit/1', // Assuming 1 is a valid resource ID
            // '/resource_edit/show/1', // Assuming 1 is a valid resource_edit ID
            // '/resource_edit/original/1', // Assuming 1 is a valid resource_edit ID
            // '/resource_edit/diff/1', // Assuming 1 is a valid resource_edit ID
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
