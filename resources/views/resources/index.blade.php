<x-app-layout>
    <x-slot name="header">
        <x-filter-bar></x-filter-bar>
    </x-slot>

    <div class="flex justify-center">
        <div class="w-3/4 p-4" id="resources-results">
            <x-resources-table :resources="$resources"></x-resources-table>
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
