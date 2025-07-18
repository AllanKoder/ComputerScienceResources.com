<?php

namespace Database\Seeders;

use App\Models\ComputerScienceResource;
use Illuminate\Database\Seeder;

class ComputerScienceResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ComputerScienceResource::factory(5)->create();
    }
}
