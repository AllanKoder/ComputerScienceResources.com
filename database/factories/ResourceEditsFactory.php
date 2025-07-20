<?php

namespace Database\Factories;

use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends Factory<ResourceEdits>
 */
class ResourceEditsFactory extends Factory
{
    protected $model = ResourceEdits::class;

    public function definition(): array
    {
        $platforms = config('computerScienceResource.platforms');
        $difficulties = config('computerScienceResource.difficulties');
        $pricings = config('computerScienceResource.pricings');

        $fakeImage = UploadedFile::fake()->image('resource_edit.jpg');
        $imagePath = $fakeImage->store('resource-edits', 'public');

        $possibleChanges = [
            'name' => $this->faker->name(),
            'description' => $this->faker->realText(),
            'image_path' => $imagePath,
            'page_url' => $this->faker->url(),
            'platforms' => $this->faker->randomElements($platforms, rand(1, 3)),
            'difficulty' => $this->faker->randomElement($difficulties),
            'pricing' => $this->faker->randomElement($pricings),
            'topic_tags' => ['data structures', 'algorithms'],
            'programming_language_tags' => ['python'],
            'general_tags' => ['interactive', 'challenging'],
        ];

        // Select a random subset of keys to include in the proposed changes.
        $proposedKeys = $this->faker->randomElements(
            array_keys($possibleChanges),
            $this->faker->numberBetween(1, count($possibleChanges))
        );

        $proposedChanges = [];
        foreach ($proposedKeys as $key) {
            $proposedChanges[$key] = $possibleChanges[$key];
        }

        return [
            'computer_science_resource_id' => function () {
                return ComputerScienceResource::inRandomOrder()->firstOr(function () {
                    return ComputerScienceResource::factory()->create();
                })->id;
            },
            'user_id' => function () {
                return User::inRandomOrder()->firstOr(function () {
                    return User::factory()->create();
                })->id;
            },
            'edit_title' => $this->faker->sentence,
            'edit_description' => $this->faker->paragraph,

            'proposed_changes' => $proposedChanges,
        ];
    }
}
