<!-- resources/views/resources/show.blade.php -->

<x-app-layout>
    <div class="mb-4">
        <!-- Displaying title, description, and image -->
        <div class="flex grid-cols-2 gap-4">
            <div class="flex items-center justify-center h-20 overflow-hidden border-2 border-gray p-1 rounded">
                <!-- Container with a set height and max-width to control the image size -->
                <div class="h-full max-w-full">
                    <img src="{{ $resource->image_url }}" class="h-full w-auto object-contain" alt="{{ $resource->title }}"/>
                </div>
            </div>
            <div class="w-10/12">    
                <span class="flex align-middle">
                    <a href="{{ $resource->resource_url }}">
                        <h2 class="text-2xl font-bold">{{ $resource->title }}</h2>
                    </a>
                    <a href="{{ $resource->resource_url }}" target=”_blank”>
                        <i class="p-2 fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </span>
                <p class="mb-1">{{ $resource->description }}</p>
            </div>
        </div>

        <!-- Pricing model -->
        <div class="flex flex-wrap">
            <p>Pricing Model: </p>
            <span class="bg-gray-200 text-gray-700 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300 mb-2">{{ $resource->pricing }}</span>
        </div>

        <!-- Content Formats -->
        <div class="flex flex-wrap">
            @if(is_array($resource->formats))
                <p>Content Formats: </p>
                @foreach ($resource->formats as $format)
                    <span class="bg-gray-200 text-gray-700 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300 mb-2">{{ $format }}</span>
                @endforeach
            @endif
        </div>
                        
        <!-- Displaying features and limitations -->
        <div class="mb-4">
            <strong>Features:</strong>
            @if(is_array($resource->features))
                <ul>
                    @foreach ($resource->features as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                </ul>
            @else
                <p>No features listed.</p>
            @endif
        </div>

        <div class="mb-4">
            <strong>Limitations:</strong>
            @if(is_array($resource->limitations))
                <ul>
                    @foreach ($resource->limitations as $limitation)
                        <li>{{ $limitation }}</li>
                    @endforeach
                </ul>
            @else
                <p>No limitations listed.</p>
            @endif
        </div>
        <!-- 'topics' is an array of strings -->
        <div class="flex flex-wrap">
            @if(is_array($resource->topics))
                <p>Topics:</p>
                @foreach ($resource->topics as $topic)
                    <span class="bg-gray-200 text-gray-700 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300 mb-2">{{ $topic }}</span>
                @endforeach
            @endif
        </div>
        
        <!-- 'Tags' is an array of strings -->
        <div class="flex flex-wrap">
            @foreach ($resource->tags as $tag)
                <!-- Access the English name of the tag -->
                <span class="bg-green-200 text-gray-700 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-green-700 dark:text-gray-300 mb-2">{{ $tag['name'] }}</span>
            @endforeach
        </div>
        
        <!-- Difficulty -->
        <div class="mb-4">
            <strong>Difficulty:</strong> {{ $resource->difficulty }}
        </div>
        
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

    @if(Auth::user())
        @include('reviews.resources.create', array('resource'=>$resource))
        @include('comments.partials.create', array('type'=>'resource', 'id'=>$resource->id))
    @endif

    
    <div class="tab-list" role="tablist">
        <button hx-get="{{ route('reviews.show', ['resource' => $resource->id]) }}" hx-target="#tab-content" hx-indicator="#spinner" class="selected bg-teal-300 p-4" role="tab" aria-selected="true" aria-controls="tab-content">Reviews</button>
        <button hx-get="{{ route('comment.comments', ['type' => 'resource', 'id' => $resource->id]) }}" hx-target="#tab-content" hx-indicator="#spinner" class="bg-teal-300 p-4" role="tab" aria-selected="false" aria-controls="tab-content">Comments</button>
        <button hx-get="{{ route('resource_edits.index', ['resource' => $resource->id]) }}" hx-target="#tab-content" hx-indicator="#spinner" class="bg-teal-300 p-4" role="tab" aria-selected="false" aria-controls="tab-content">Propose Edits</button>
    </div>
    
    <!-- Loading bar -->
    <x-spinner class="mx-auto" id="spinner"></x-spinner>
    <div id="tab-content" role="tabpanel" class="tab-content">
        @if($commentTree->isNotEmpty())
            @include('comments.partials.index', ['comments' => $commentTree])
        @else
            <p>No comments available.</p>
        @endif   
    </div>
</x-app-layout>
