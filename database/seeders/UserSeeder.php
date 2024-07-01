<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Allan Kong',
            'email' => 'allankong176@gmail.com',
            'password' => Hash::make('Admin123!'),
        ]);
        
        User::factory()->create([
            'name' => 'Faker Kong',
            'email' => 'thedulme@gmail.com',
            'password' => Hash::make('password'),
        ]);
    }
}
