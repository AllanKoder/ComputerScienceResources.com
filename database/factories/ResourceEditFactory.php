<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\User;
use App\Models\ResourceEdit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResourceEdit>
 */
class ResourceEditFactory extends Factory
{
    protected $model = ResourceEdit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Get a random resource or create one if none exist
        $resource = Resource::inRandomOrder()->first() ?? Resource::factory()->create();

        // Get a random user or create one if none exist
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'resource_id' => $resource->id,
            'user_id' => $user->id,
            'edit_title' => fake()->sentence,
            'edit_description' => fake()->paragraph,
        ];
    }
}
