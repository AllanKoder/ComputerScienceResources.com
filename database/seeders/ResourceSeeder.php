<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Resource;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 3 new resources
        $resource1 = Resource::factory()->create([
            'title' => 'Learning Laravel',
            'description' => 'A comprehensive guide to web development with Laravel.',
            'image_url' => 'https://wallpapers.com/images/featured/funny-cats-pictures-uu9qufqc5zq8l7el.jpg',
            'formats' => ['website', 'book'],
            'features' => ['Comprehensive', 'Beginner-friendly'],
            'limitations' => ['Requires basic PHP knowledge'],
            'resource_url' => 'https://leetcode.com',
            'topics' => ['laravel', 'web development'],
            'difficulty' => 'beginner',
        ]);

        $resource1->attachTags(["tag1", "php", "laravel"]);

        Resource::factory()->create([
            'title' => 'Advanced Eloquent Usage',
            'description' => 'Deep dive into the Eloquent ORM and its advanced features.',
            'image_url' => 'https://wallpapers.com/images/featured/funny-cats-pictures-uu9qufqc5zq8l7el.jpg',
            'formats' => ['online_course', 'video'],
            'features' => ['In-depth', 'Expert instructors'],
            'limitations' => ['Advanced level'],
            'resource_url' => 'https://leetcode.com',
            'topics' => ['eloquent', 'orm'],
            'difficulty' => 'industry',
        ]);

        Resource::factory()->create([
            'title' => 'The Art of API Design',
            'description' => 'Best practices for designing robust APIs with Laravel.',
            'image_url' => 'https://wallpapers.com/images/featured/funny-cats-pictures-uu9qufqc5zq8l7el.jpg',
            'formats' => ['article', 'research_paper'],
            'features' => ['Best Practices', 'Case Studies'],
            'limitations' => ['High-level concepts'],
            'resource_url' => 'https://leetcode.com',
            'topics' => ['api design', 'laravel'],
            'difficulty' => 'academic',
        ]);

        Resource::factory(60)->create();
    }
}
