<?php

namespace Database\Factories;

use App\Models\ResourceEdit;
use App\Models\ProposedEdit;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProposedEdit>
 */
class ProposedEditFactory extends Factory
{
    protected $model = ProposedEdit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Get a random resource edit or create one if none exist
        $resourceEdit = ResourceEdit::inRandomOrder()->first() ?? ResourceEdit::factory()->create();

        // Get a random field name from the resource attributes
        $fieldNames = Resource::getResourceAttributes();
        $fieldName = fake()->randomElement($fieldNames);

        // Determine the type of the field and generate appropriate fake data
        $newValue = match ($fieldName) {
            'formats' => fake()->randomElement(array_values(config('formats'))),
            'features', 'limitations', 'topics' => fake()->words(3), // Array fields
            'image_url' => fake()->imageUrl(),
            'pricing' => fake()->randomElement(array_values(config('pricings'))),
            'difficulty' => fake()->randomElement(array_values(config('difficulties'))),
            'tag_names' => fake()->words(5), // Array field
            default => fake()->sentence, // String fields
        };

        return [
            'resource_edit_id' => $resourceEdit->id,
            'field_name' => $fieldName,
            'new_value' => is_array($newValue) ? json_encode($newValue) : $newValue,
        ];
    }
}
