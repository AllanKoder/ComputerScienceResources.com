<?php

namespace App\Console\Commands;

use App\Models\ComputerScienceResource;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.xml file';

    public function handle()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/')) // homepage
            ->add(Url::create('/about')) // about page example
            ->add(Url::create('/login'))
            ->add(Url::create('/register'));

        // Add each resource page to the sitemap with last modification date if available
        // Limit to a maximum of 30,000 resources
        $limit = 30000;
        $count = 0;

        $query = ComputerScienceResource::query()->orderBy('id')->limit($limit);
        foreach ($query->cursor() as $resource) {
            if ($count >= $limit) {
                break;
            }

            $lastMod = $resource->updated_at ?? $resource->created_at;

            $sitemap->add(
                Url::create(route('resources.show', ['slug' => $resource->slug]))
                    ->setLastModificationDate($lastMod)
            );

            $count++;
        }

        // Write to public/ so it's directly accessible
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
}
