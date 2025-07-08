<?php

namespace App\Listeners;

use App\Events\TagFrequencyChanged;
use App\Models\TagFrequency;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class ModifyTagFrequency
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TagFrequencyChanged $event): void
    {
        $old = $event->oldTags ?? [];
        $new = $event->newTags ?? [];

        // Build diffs for every tag
        $diffs = [];
        foreach (array_unique(array_merge(array_keys($old), array_keys($new))) as $tag) {
            $diff = ($new[$tag] ?? 0) - ($old[$tag] ?? 0);
            if ($diff !== 0) {
                $diffs[$tag] = $diff;
            }
        }

        if (empty($diffs)) {
            return;
        }

        // One upsert: increment (or decrement) existing, insert new
        $upserts = [];
        foreach ($diffs as $tag => $count) {
            $upserts[] = [
                'tag' => $tag,
                'count' => $count,
            ];
        }

        DB::table('tag_frequencies')->upsert(
            $upserts,
            ['tag'],
            [
                'count' => DB::raw('tag_frequencies.count + VALUES(count)'),
            ]
        );

        // Clean out any zero-or-negative counts
        DB::table('tag_frequencies')
            ->where('count', '<=', 0)
            ->delete();
    }
}
