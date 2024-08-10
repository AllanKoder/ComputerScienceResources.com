<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'comment_text' => fake()->text,
            'comment_title' => fake()->realTextBetween(1,50),
            'user_id' => fake()->numberBetween(1, 10),
            'commentable_id' => fake()->numberBetween(1,10),
            'commentable_type' => Resource::class,
        ];
    }
}
