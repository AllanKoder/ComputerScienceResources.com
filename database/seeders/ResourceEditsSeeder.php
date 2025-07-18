<?php

namespace Database\Seeders;

use App\Models\ResourceEdits;
use Illuminate\Database\Seeder;

class ResourceEditsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ResourceEdits::factory(10)->create();
    }
}
