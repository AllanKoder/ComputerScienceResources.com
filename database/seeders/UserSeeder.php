<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\ComputerScienceResourceFactory;
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

        User::factory()->create(
            [
                'name'=>'Allan Kong',
                'email'=>'allankong176@gmail.com',
                'password'=>'password',
            ]
        );
    }
}
