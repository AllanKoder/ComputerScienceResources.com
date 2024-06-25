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
        // Clear the existing resources table
        \DB::table('resources')->delete();

        // Create 3 new resources
        Resource::create([
            'title' => 'Learning Laravel',
            'description' => 'A comprehensive guide to web development with Laravel.',
            'image_url' => 'https://wallpapers.com/images/featured/funny-cats-pictures-uu9qufqc5zq8l7el.jpg',
            'formats' => ['website', 'book'],
            'features' => ['Comprehensive', 'Beginner-friendly'],
            'limitations' => ['Requires basic PHP knowledge'],
            'resource_url' => 'https://leetcode.com',
            'pricing' => 'freemium',
            'topics' => ['Laravel', 'Web Development'],
            'difficulty' => 'beginner',
        ]);

        Resource::create([
            'title' => 'Advanced Eloquent Usage',
            'description' => 'Deep dive into the Eloquent ORM and its advanced features.',
            'image_url' => 'https://wallpapers.com/images/featured/funny-cats-pictures-uu9qufqc5zq8l7el.jpg',
            'formats' => ['online_course', 'video'],
            'features' => ['In-depth', 'Expert instructors'],
            'limitations' => ['Advanced level'],
            'resource_url' => 'https://leetcode.com',
            'pricing' => 'subscription',
            'topics' => ['Eloquent', 'ORM'],
            'difficulty' => 'industry',
        ]);

        Resource::create([
            'title' => 'The Art of API Design',
            'description' => 'Best practices for designing robust APIs with Laravel.',
            'image_url' => 'https://wallpapers.com/images/featured/funny-cats-pictures-uu9qufqc5zq8l7el.jpg',
            'formats' => ['article', 'research_paper'],
            'features' => ['Best Practices', 'Case Studies'],
            'limitations' => ['High-level concepts'],
            'resource_url' => 'https://leetcode.com',
            'pricing' => 'paid',
            'topics' => ['API Design', 'Laravel'],
            'difficulty' => 'academic',
        ]);
    }
}
