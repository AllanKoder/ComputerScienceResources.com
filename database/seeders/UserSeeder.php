<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
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

        if (! User::where('name', 'Allan Kong')->exists()) {
            User::factory()->create(
                [
                    'name' => 'Allan Kong',
                    'email' => 'allankong176@gmail.com',
                    'password' => Hash::make('password'),
                ]
            );
        }

        if (! User::where('name', 'admin')->exists()) {
            User::factory()->create(
                [
                    'name' => 'admin',
                    'email' => 'admin@computerscienceresources.com',
                    'password' => Hash::make('test123'),
                ]
            );
        }
    }
}
