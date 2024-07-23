<h1>Resource Edits</h1>

<h1><a href="{{ route('resource_edits.create', ['resource' => $resource]) }}">Create Resource Edit</a></h1>

<div class="pb-8">
    @if($resourceEdits->isEmpty())
        <p>No edits found for this resource.</p>
    @else
        <ul>
            @foreach($resourceEdits as $edit)
                <li>
                    <h2>{{ $edit->title }}</h2>
                    <p>{{ $edit->description }}</p>
                    <p><strong>Created at:</strong> {{ $edit->created_at }}</p>
                </li>
            @endforeach
        </ul>
    @endif
</div>
