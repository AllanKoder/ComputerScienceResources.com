<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $resource = Resource::inRandomOrder()->first() ?? Resource::factory()->create();

        return [
            'comment_text' => fake()->text,
            'user_id' =>  $user->id,
            'commentable_id' => $resource->id,
            'commentable_type' => Resource::class,
        ];
    }
}
