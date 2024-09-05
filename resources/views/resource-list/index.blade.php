<!-- resources/views/resource-list/index.blade.php -->

<x-app-layout>
    <div class="container">
        <h1>My Resource Lists</h1>
        @if($resourceLists->isEmpty())
            <p>You have no resource lists.</p>
        @else
            <ul>
                @foreach($resourceLists as $list)
                    <li>
                        <h2>{{ $list->name }}</h2>
                        <p> {{$list->description }} </p>
                        <ul>
                            @foreach($list->items as $item)
                                <li>{{ $item->description }}</li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-app-layout>