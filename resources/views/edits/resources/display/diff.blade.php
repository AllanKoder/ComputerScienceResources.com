<!-- resources/views/components/resource-diff.blade.php -->
<div class="flex grid-cols-2 gap-4">
    @if(isset($diffs['image_url']))
        <div class="flex items-center justify-center h-20 overflow-hidden border-2 border-gray p-1 rounded">
            <!-- Container with a set height and max-width to control the image size -->
            <div class="h-full max-w-full">
                <img src="{{ $diffs['image_url'] }}" class="h-full w-auto object-contain" alt="{{ $diffs['image_url'] }}"/>
            </div>
        </div>
    @endif

    <div class="w-10/12">
        @if(isset($diffs['title']))
            <p class="mb-1">
                <p class="font-bold text-2xl">Title:</p>
                <x-diff.view-diff-text :diff="$diffs['title']" />
            </p>
        @endif

        @if(isset($diffs['resource_url']))
            <p class="mb-1">
                <p class="font-bold">Resource URL:</p>
                <x-diff.view-diff-text :diff="$diffs['resource_url']" />
            </p>
        @endif

        @if(isset($diffs['description']))
            <p class="mb-1">
                <p class="font-bold">Description:</p>
                <x-diff.view-diff-text :diff="$diffs['description']" />
            </p>
        @endif

        @if(isset($diffs['image_url']))
            <p class="mb-1">
                <p class="font-bold">Image URL:</p>
                <x-diff.view-diff-text :diff="$diffs['image_url']" />
            </p>
        @endif
    </div>
</div>

@if(isset($diffs['pricing']))
    <div class="mb-2">
        <p class="font-bold">Pricing Model: </p>
        <x-diff.view-diff-text :diff="$diffs['pricing']" />
    </div>
@endif

@if(isset($diffs['formats']))
    <div class="mb-2">
        <p class="font-bold">Content Formats: </p>
        <x-diff.view-diff-set :diff="$diffs['formats']" />
    </div>
@endif

@if(isset($diffs['features']))
    <div class="mb-2">
        <p class="font-bold">Feature: </p>
        <x-diff.view-diff-set :diff="$diffs['features']" />
    </div>
@endif

@if(isset($diffs['limitations']))
    <div class="mb-2">
        <p class="font-bold">Limitations: </p>
        <x-diff.view-diff-set :diff="$diffs['limitations']" />
    </div>
@endif

@if(isset($diffs['topics']))
    <div class="mb-2">
        <p class="font-bold">Topics: </p>
        <x-diff.view-diff-set :diff="$diffs['topics']" />
    </div>
@endif

@if(isset($diffs['tags']))
    <div class="mb-2">
        <p class="font-bold">Tags: </p>
        <x-diff.view-diff-set :diff="$diffs['tags']" />
    </div>
@endif

@if(isset($diffs['difficulty']))
    <div class="mb-2">
        <p class="font-bold">Difficulty: </p>
        <x-diff.view-diff-text :diff="$diffs['difficulty']" />
    </div>
@endif
