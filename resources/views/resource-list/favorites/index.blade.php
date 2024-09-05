<x-app-layout>

    <div class="container">
        <h1>My Favorites Lists</h1>
        <ul>
            <li>
                <h1 class="font-bold">Favorites</h1>
                @if($favorites->isEmpty())
                    <p>You have no favorites.</p>
                @else
                    <ul>
                        @foreach($favorites as $item)
                            <a href="{{ route('resources.show', ['id' => $item->resource->id]) }}">
                                <p>{{ $item->resource->title }}</p>
                            </a>
                        @endforeach
                    </ul>
                @endif
            </li>
        </ul>
    </div>

</x-app-layout>
