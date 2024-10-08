@props(['resources'])

@foreach ($resources as $resource)
    <div class="mb-8">
        <!-- Displaying title, description, and image -->
        <div class="flex grid-cols-2 gap-4">
            <div class="flex items-center justify-center h-20 overflow-hidden border-2 border-gray p-1 rounded">
                <!-- Container with a set height and max-width to control the image size -->
                <div class="h-full max-w-full">
                    <img loading="lazy" src="{{ $resource->image_url }}" class="h-full w-auto object-contain" alt="{{ $resource->title }}"/>
                </div>
            </div>
            <div class="w-10/12">    
                <span class="flex align-middle">
                    <a href="{{ route('resources.show', ['id' => $resource->id]) }}">
                        <h2 class="text-2xl font-bold">{{ $resource->title }}</h2>
                    </a>
                    <a href="{{ $resource->resource_url }}" target="_blank">
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
    </div>
@endforeach

<!-- retain query parameters -->
{{ $resources->appends(request()->query())->links() }}
