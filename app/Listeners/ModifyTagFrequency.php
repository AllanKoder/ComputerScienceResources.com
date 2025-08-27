<?php

namespace App\Listeners;

use App\Events\TagFrequencyChanged;
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
     * Update tag frequencies based on two arrays of tags.
     *
     * @param array $oldTags Array of old tags (e.g. ['php', 'laravel', ...])
     * @param array $newTags Array of new tags (e.g. ['php', 'vue', ...])
     */
    public function handle(TagFrequencyChanged $event): void
    {
        $tagType = $event->tagType;
        // Count tags in each array
        $old = array_count_values($event->oldTags);
        $new = array_count_values($event->newTags);

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

        // Only upsert for the given tagType
        $upserts = [];
        foreach ($diffs as $tag => $count) {
            $upserts[] = [
                'tag' => $tag,
                'type' => $tagType,
                'count' => $count,
            ];
        }

        // https://laravel.com/docs/12.x/queries#upserts
        DB::table('tag_frequencies')->upsert(
            $upserts,
            ['tag', 'type'],
            [
                'count' => DB::raw('tag_frequencies.count + VALUES(count)'),
            ]
        );

        // Clean out any zero-or-negative counts for this tagType only
        DB::table('tag_frequencies')
            ->where('type', $tagType)
            ->where('count', '<=', 0)
            ->delete();
    }
}
