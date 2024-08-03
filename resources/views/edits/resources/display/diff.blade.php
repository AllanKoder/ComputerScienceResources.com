<!-- resources/views/components/resource-diff.blade.php -->
<div class="flex grid-cols-2 gap-4">
    <div class="flex items-center justify-center h-20 overflow-hidden border-2 border-gray p-1 rounded">
        <!-- Container with a set height and max-width to control the image size -->
        <div class="h-full max-w-full">
            <img src="{{ $editedResource->image_url }}" class="h-full w-auto object-contain" alt="{{ $editedResource->image_url }}"/>
        </div>
    </div>
    <div class="w-10/12">    
        <span class="flex align-middle">
            <a href="{{ $diffs['resource_url'] }}">
                <h2 class="text-2xl font-bold">
                    <x-view-diff-text :diff="$diffs['title']" />
                </h2>
            </a>
            <a href="{{ $diffs['resource_url'] }}" target=”_blank”>
                <i class="p-2 fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </span>
        <p class="mb-1">
            <p class="font-bold">Description:</p>
            <x-view-diff-text :diff="$diffs['description']" />
        </p>
        <p class="mb-1">
            <p class="font-bold">Image URL:</p>
            <x-view-diff-text :diff="$diffs['image_url']" />
        </p>
        <p class="mb-1">
            <p class="font-bold">Resource URL:</p>
            <x-view-diff-text :diff="$diffs['resource_url']" />
        </p>
    </div>
</div>

<div class="mb-2">
    <p class="font-bold">Pricing Model: </p>
    <x-view-diff-text :diff="$diffs['pricing']" />
</div>  

<div class="mb-2">
    <p class="font-bold">Content Formats: </p>
    <x-view-diff-set :diff="$diffs['formats']" />
</div>
                    
<div class="mb-2">
    <p class="font-bold">Feature: </p>
    <x-view-diff-set :diff="$diffs['features']" />
</div>

<div class="mb-2">
    <p class="font-bold">Limitations: </p>
    <x-view-diff-set :diff="$diffs['limitations']" />
</div>

<div class="mb-2">
    <p class="font-bold">Topics: </p>
    <x-view-diff-set :diff="$diffs['topics']" />
</div>

<div class="mb-2">
    <p class="font-bold">Tags: </p>
    @if(isset($diffs['tags']) && is_array($resource->tags))
        <x-view-diff-set :diff="$diffs['tags']" />
    @endif
</div>

<div class="mb-2">
    <p class="font-bold">Difficulty: </p>
        <x-view-diff-text :diff="$diffs['difficulty']" />
</div>
