<?php

namespace App\Services;

use App\Models\ComputerScienceResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeStatisticsService
{
    public function __construct()
    {

    }

    function getPublicUrl(?string $path): ?string {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    private function resourceTop(): Collection
    {
        return DB::table('computer_science_resources')
            ->whereNotNull('image_path')
            ->limit(10)
            ->get()
            ->map(fn($res) => [
                'id' => $res->id,
                'image_url' => $this->getPublicUrl($res->image_path),
            ]);
    }

    private function resourcesCount(): int
    {
        return DB::table('computer_science_resources')->count();
    }

    private function topTopics(): Collection
    {
        return DB::table('tag_frequencies')->where('type','topics_tags')
            ->orderByDesc('count')->limit(10)->get();
    }

    private function topicsCount(): int
    {
        return DB::table('tag_frequencies')->where('type','topics_tags')->count();
    }


    public function getStatistics()
    {
        return array(
            "resources_top" => $this->resourceTop(),
            "resources_count" => $this->resourcesCount(),
            "topics_count" => $this->topicsCount(),
            "topics_top" => $this->topTopics()
        );
    }

}
