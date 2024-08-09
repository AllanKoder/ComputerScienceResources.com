
<x-app-layout>
    <div class="flex justify-between mb-4">
        <!-- Original Resource -->
        <div class="w-1/2 pr-2 border-r-4 border-gray-400">
            <h2 class="text-xl font-bold mb-4">Original Resource</h2>
            <div class="flex items-center mb-4 border-b-2 h-9">
                <!-- Navigation Bar -->
                <button hx-get="{{ route('resource_edits.original', ['resource_edit'=>$resourceEdit->id]) }}" hx-target="#edited" hx-swap="innerHTML" class="bg-gray-200 px-2 py-1">
                    View Original
                </button>
                <button hx-get="{{ route('resource_edits.diff', ['resource_edit'=>$resourceEdit->id]) }}" hx-target="#edited" hx-swap="innerHTML" class="bg-gray-200 px-2 py-1">
                    View Diff
                </button>
            </div>
            <div id="edited">
                <x-resource-details :resource="$resourceEdit->resource" />
            </div>
        </div>
            
            <!-- Proposed Edit -->
        <div class="w-1/2 pl-2">
            <h2 class="text-xl font-bold mb-4">Proposed Edit</h2>
            <div class="flex items-center justify-start mb-4 border-b-2 h-9">
                <div class="flex items-center space-x-4 px-5">
                    <!-- Downvotes Button -->
                    <form action="{{ route('votes.vote', ['type' => 'resourceEdit', 'id' => $resourceEdit->id]) }}" method="POST">
                        @csrf
                        <button type="submit" name="vote_value" value="-1" class="reject-button flex items-center px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                            <i class="fa-solid fa-x mr-2"></i> Reject <span class="ml-2" id="downvotes">{{ $disapprovals }}</span>
                        </button>
                    </form>
                                       
                    <!-- Upvotes Button -->
                    <form action="{{ route('votes.vote', ['type' => 'resourceEdit', 'id' => $resourceEdit->id]) }}" method="POST">
                        @csrf
                        <button type="submit" name="vote_value" value="1" class="approve-button flex items-center px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400">
                            <i class="fa-solid fa-check mr-2"></i> Approve <span class="ml-2" id="upvotes">{{ $approvals }}</span>
                        </button>
                    </form>

                    <!-- Merge Button -->
                    <form action="{{ route('resource_edits.merge', ['resource_edit'=>$resourceEdit->id]) }}" method="POST">
                        @csrf
                        <button class="approve-button flex items-center px-4 py-2 bg-gray-500 text-white rounded focus:outline-none focus:ring-2 focus:ring-gray-400">
                            Merge Changes <span class="ml-2" id="upvotes">{{ $totalVotes }}</span>
                        </button>
                    </form>
                    
                </div>
            </div>
                                    
            <div>
                <x-resource-details :resource="$editedResource" />
            </div>
        </div>
    </div>
</x-app-layout>
