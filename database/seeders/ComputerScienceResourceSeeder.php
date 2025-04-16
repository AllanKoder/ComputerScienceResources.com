<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ComputerScienceResource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ComputerScienceResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ComputerScienceResource::factory(1)->create();
    }
}
