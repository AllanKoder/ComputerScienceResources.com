
<x-app-layout>
    <div class="flex justify-between mb-4">
        <!-- Original Resource -->
        <div class="w-1/2 pr-2 border-r-4 border-gray-400">
            <h2 class="text-xl font-bold mb-4">Original Resource</h2>
            <div class="flex items-center justify-between mb-4 border-b-2 h-9">
                <!-- Navigation Bar -->
                <div class="flex items-center space-x-4 px-5">
                </div>
            </div>
            <div>
                <x-resource-details :resource="$resourceEdit->resource" />
            </div>
        </div>

        <!-- Proposed Edit -->
        <div class="w-1/2 pl-2">
            <h2 class="text-xl font-bold mb-4">Proposed Edit</h2>
            <div class="flex items-center justify-start mb-4 border-b-2 h-9">
                <button hx-get="{{ route('resource_edits.edits', ['resource_edit'=>$resourceEdit->id]) }}" hx-target="#edited" hx-swap="innerHTML" class="bg-gray-200 px-2 py-1">
                    View Result
                </button>
                <button hx-get="{{ route('resource_edits.diff', ['resource_edit'=>$resourceEdit->id]) }}" hx-target="#edited" hx-swap="innerHTML" class="bg-gray-200 px-2 py-1">
                    View Diff
                </button>
            </div>
            <div id="edited">
                <x-resource-details :resource="$editedResource" />
            </div>
        </div>
    </div>
</x-app-layout>
