<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ComputerScienceResource;
use Illuminate\Support\Facades\DB;

class UpdateTagFrequencies extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tags:update-frequencies';

    /**
     * The console command description.
     */
    protected $description = 'Recalculate and update the tag frequencies for all tag types.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting tag frequency update...');

        $tagTypes = [
            'programming_languages_tags',
            'topics_tags',
            'general_tags',
        ];

        foreach ($tagTypes as $type) {
            $this->updateType($type);
        }

        $this->info('✅ Tag frequency update complete!');
        return self::SUCCESS;
    }

    /**
     * Update frequencies for a specific tag type.
     */
    protected function updateType(string $type): void
    {
        $this->line("Processing {$type}...");

        // Flatten all tags for this type across all resources
        $tags = ComputerScienceResource::all()
            ->flatMap(fn ($r) => $r->{$type})
            ->filter()
            ->map(fn ($tag) => trim($tag))
            ->toArray();

        // Count occurrences
        $frequencies = array_count_values($tags);

        foreach ($frequencies as $tag => $count) {
            DB::table('tag_frequencies')->updateOrInsert(
                ['tag' => $tag, 'type' => $type],
                ['count' => $count]
            );
        }

        $this->line("Updated " . count($frequencies) . " entries for {$type}.");
    }
}
