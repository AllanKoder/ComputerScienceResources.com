<x-app-layout>
    <x-slot name="header">
        @include('layouts.searchbar')
    </x-slot>

    <div class="flex justify-center">
        <div class="w-3/4 p-4">
            @foreach ($resources as $resource)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-2">{{ $resource->title }}</h2>
                    <p class="mb-4">{{ $resource->description }}</p>
                    <!-- Displaying features and limitations -->
                    <div class="mb-4">
                        <strong>Features:</strong>
                        <ul>
                            @foreach ($resource->features as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mb-4">
                        <strong>Limitations:</strong>
                        <ul>
                            @foreach ($resource->limitations as $limitation)
                                <li>{{ $limitation }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- Assuming 'topics' is an array of strings -->
                    <div class="flex flex-wrap">
                        @foreach ($resource->topics as $topic)
                            <span class="bg-gray-200 text-gray-700 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300 mb-2">{{ $topic }}</span>
                        @endforeach
                    </div>
                    <div class="mb-4">
                        <strong>Difficulty:</strong> {{ $resource->difficulty }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="w-1/4 p-4 border-l-2 border-gray-300">
            <form action="{{ route('resources.create') }}" method="GET">
                <button type="submit" class="bg-brand text-white font-bold py-2 px-4 rounded">
                    Create a Resource!
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
