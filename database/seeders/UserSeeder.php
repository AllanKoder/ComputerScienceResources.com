<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\ComputerScienceResourceFactory;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Running UserSeeder');
        User::factory(10)->create();

        if (!User::where('name','Allan Kong')->exists()) {
            User::factory()->create(
                [
                    'name' => 'Allan Kong',
                    'email' => 'allankong176@gmail.com',
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
