<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\ComputerScienceResourceSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ResourceReviewSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ComputerScienceResourceSeeder::class,
            ResourceReviewSeeder::class,
        ]);
    }
}
