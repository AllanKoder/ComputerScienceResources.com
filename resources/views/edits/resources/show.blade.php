
<x-app-layout>
    <div class="flex justify-between mb-4">
        <!-- Original Resource -->
        <div class="w-1/2 pr-2 border-r-4 border-gray-400">
            <h2 class="text-xl font-bold mb-4">Original Resource</h2>
            <x-resource-details :resource="$resourceEdit->resource" />
        </div>

        <!-- Proposed Edit -->
        <div class="w-1/2 pl-2">
            <h2 class="text-xl font-bold mb-4">Proposed Edit</h2>
            {{-- <x-resource-details :resource="$editedResource" /> --}}
            @php
                $textDiff = ([
                    ['type' => 'normal', 'text' => 'This is '],
                    ['type' => 'insertion', 'text' => 'an example '],
                    ['type' => 'normal', 'text' => 'of '],
                    ['type' => 'deletion', 'text' => 'text '],
                    ['type' => 'normal', 'text' => 'differences.']
                ]);
                $setDiff = ([
                    'common' => ['This is', 'of', 'differences.'],
                    'insertions' => ['an example', 'text'],
                    'deletions' => ['old text', 'another example']
                ]);
            @endphp

            <x-view-set-diff :setDiff="$setDiff"></x-view-set-diff>
            <x-view-diff-text :textDiff="$textDiff">text</x-view-diff-text>
        </div>
    </div>
</x-app-layout>
