<x-app-layout>
    <x-slot:header>
        @include('layouts.searchbar')
    </x-slot:header>

    <div class="flex justify-center">
        <div class="w-3/4 p-4">
            @foreach ($resources as $resource)
                <div class="mb-8">
                    <img src="{{ $resource->image }}" alt="Resource Image" class="w-full h-auto mb-2">
                    <h2 class="text-2xl font-bold mb-2">{{ $resource->title }}</h2>
                    <p class="mb-4">{{ $resource->description }}</p>
                    <div class="flex flex-wrap">
                        @foreach ($resource->tags as $tag)
                            <span class="bg-gray-200 text-gray-700 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300 mb-2">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="w-1/4 p-4 border-l-2 border-gray-300 flex">
            <form action="{{ route('resources.create') }}" method="GET">
                <button type="submit" class="bg-brand hover:bg-brand-dark text-white font-bold py-2 px-4 rounded">
                    Create a Resource!
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
