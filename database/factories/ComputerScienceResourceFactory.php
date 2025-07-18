<?php

namespace Database\Factories;

use App\Events\TagFrequencyChanged;
use App\Models\ComputerScienceResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComputerScienceResource>
 */
class ComputerScienceResourceFactory extends Factory
{
    protected $model = ComputerScienceResource::class;

    protected ?array $topicTags = null;

    protected ?array $programmingLanguageTags = null;

    protected ?array $generalTags = null;

    public function definition(): array
    {
        $platforms = config('computerScienceResource.platforms');
        $difficulties = config('computerScienceResource.difficulties');
        $pricings = config('computerScienceResource.pricings');

        $fakeImage = UploadedFile::fake()->image('resource.jpg');
        $imagePath = $fakeImage->store('resource', 'public');

        return [
            'name' => fake()->name(),
            'description' => fake()->realText(),
            'user_id' => User::inRandomOrder()->first() ?? User::factory()->create(),
            'image_path' => $imagePath,
            'page_url' => fake()->url(),
            'platforms' => fake()->randomElements($platforms, rand(1, 3)),
            'difficulty' => fake()->randomElement($difficulties),
            'pricing' => fake()->randomElement($pricings),
        ];
    }

    public function setTags(array $topic = [], array $language = [], array $general = []): static
    {
        $this->topicTags = $topic;
        $this->programmingLanguageTags = $language;
        $this->generalTags = $general;

        return $this;
    }

    public function configure(): Factory
    {
        return $this->afterCreating(function (ComputerScienceResource $resource) {
            $fakerTags = ['tag1', 'tag2', 'tag3', 'tag4', 'tag5', fake()->word(), fake()->word()];

            $resource->topic_tags = $this->topicTags ?? fake()->randomElements($fakerTags, fake()->numberBetween(3, count($fakerTags)));
            $resource->programming_language_tags = $this->programmingLanguageTags ?? fake()->randomElements($fakerTags);
            $resource->general_tags = $this->generalTags ?? fake()->randomElements($fakerTags);

            TagFrequencyChanged::dispatch(null, $resource->tagCounter());
        });
    }
}
