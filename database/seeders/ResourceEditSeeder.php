<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resource;
use App\Models\ResourceEdit;
use App\Models\ProposedEdit;

class ResourceEditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Resource::take(5)->get()->each(function ($resource) {
            $resourceEdit = ResourceEdit::factory()->create(['resource_id' => $resource->id]);

            // Create a proposed edit for each field in the resource model
            foreach (Resource::getResourceAttributes() as $fieldName) {
                $newValue = match ($fieldName) {
                    'formats' => fake()->randomElement(array_values(config('formats'))),
                    'features', 'limitations', 'topics' => fake()->words(3), // Array fields
                    'image_url' => fake()->imageUrl(),
                    'resource_url' => fake()->imageUrl(),
                    'pricing' => fake()->randomElement(array_values(config('pricings'))),
                    'difficulty' => fake()->randomElement(array_values(config('difficulties'))),
                    'tag_names' => fake()->words(5), // Array field
                    default => fake()->sentence, // String fields
                };

                ProposedEdit::factory()->create([
                    'resource_edit_id' => $resourceEdit->id,
                    'field_name' => $fieldName,
                    'new_value' => json_encode($newValue),
                ]);
            }
        });
    }
}
