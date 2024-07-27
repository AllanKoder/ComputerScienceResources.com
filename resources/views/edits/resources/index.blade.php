<h1>Resource Edits</h1>

<h1><a href="{{ route('resource_edits.create', ['resource' => $resource]) }}">Create Resource Edit</a></h1>

<div class="pb-8">
    @if($resourceEdits->isEmpty())
        <p>No edits found for this resource.</p>
    @else
        <ul>
            @foreach($resourceEdits as $edit)
                <li>
                    <a href="{{ route('resource_edits.show', ['resource_edit'=>$edit->id]) }}">
                        <h2 class="text-2xl font-bold">{{ $edit->edit_title }}</h2>
                    </a>
                    <h2>{{ $edit->user->name }}</h2>
                    <p>{{ $edit->edit_description }}</p>
                </li>
            @endforeach
        </ul>
    @endif
</div>
