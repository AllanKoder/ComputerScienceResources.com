<!-- resources/views/resources/show.blade.php -->

<x-app-layout>
    <div class="mb-4">
        <!-- Displaying title, description, and image -->
        <x-resource-details :resource="$resource" />
        
        @if($reviewSummaryData)
            @include('reviews.summary.show', array('reviewSummaryData'=>$reviewSummaryData))
        @endif

        <!-- Upvote and Downvote buttons -->
        <form action="{{ route('votes.vote', ['type'=>'resource', 'id'=>$resource->id]) }}" method="POST">
            @csrf
            <button type="submit" name="vote_value" value="1" class="bg-blue-500 text-white px-4 py-2 rounded">Upvote</button>
            <button type="submit" name="vote_value" value="-1" class="bg-red-500 text-white px-4 py-2 rounded">Downvote</button>
        </form>

        <!-- Display total votes -->
        <div id="total-votes">
            Total Votes: <span>{{ $totalUpvotes ?? 0 }}</span>
        </div>
        @include('reports.create', array('type'=>'resource', 'id'=>$resource->id))
        
    </div>
    
    <div class="tab-list" role="tablist">
        <button hx-get="{{ route('reviews.index', ['resource' => $resource->id]) }}" hx-target="#tab-content" hx-indicator="#spinner" class="selected bg-teal-300 p-4" role="tab" aria-selected="true" aria-controls="tab-content">Reviews</button>
        <button hx-get="{{ route('comment.comments', ['type' => 'resource', 'id' => $resource->id]) }}" hx-target="#tab-content" hx-indicator="#spinner" class="bg-teal-300 p-4" role="tab" aria-selected="false" aria-controls="tab-content">Comments</button>
        <button hx-get="{{ route('resource_edits.index', ['resource' => $resource->id]) }}" hx-target="#tab-content" hx-indicator="#spinner" class="bg-teal-300 p-4" role="tab" aria-selected="false" aria-controls="tab-content">Propose Edits</button>
    </div>
    
    <!-- Loading bar -->
    <x-spinner class="mx-auto" id="spinner"></x-spinner>
    <div id="tab-content" role="tabpanel" class="tab-content">
        @if($commentTree->isNotEmpty())
            @include('comments.partials.index', ['comments' => $commentTree, 'id'=> $resource->id])
        @else
            <p>No comments available.</p>
        @endif   
    </div>
</x-app-layout>
