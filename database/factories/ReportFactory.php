<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'report_text' => fake()->text,
            'reportable_id' => fake()->numberBetween(1, 100),
            'reportable_type' => fake()->randomElement(['App\Models\Post', 'App\Models\Comment']),
            'user_id' => $user->id,
        ];
    }
}
