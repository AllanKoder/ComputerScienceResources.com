<?php

namespace Database\Factories;

use App\Models\UpvoteSummary;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class UpvoteSummaryFactory extends Factory
{
    protected $model = UpvoteSummary::class;

    public function definition(): array
    {
        $upvotes = $this->faker->numberBetween(0, 100);
        $downvotes = $this->faker->numberBetween(0, 100);

        return [
            'upvotes' => $upvotes,
            'downvotes' => $downvotes,
        ];
    }

    /**
     * Link the summary to a given upvotable model.
     */
    public function forUpvotable(Model $model): static
    {
        return $this->state([
            'upvotable_id' => $model->getKey(),
            'upvotable_type' => $model::class,
        ]);
    }
}
