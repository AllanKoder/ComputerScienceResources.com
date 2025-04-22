<?php

namespace Database\Seeders;

use App\Models\ResourceReview;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResourceReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ResourceReview::factory(20)->create();
    }
}
