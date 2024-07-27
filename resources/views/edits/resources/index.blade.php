<h1><a href="{{ route('resource_edits.create', ['resource' => $resource]) }}">Create Resource Edit</a></h1>


<h1>Resource Edits</h1>
<div class="pb-8">
    @if($resourceEdits->isEmpty())
        <p>No edits found for this resource.</p>
    @else
        <ul>
            @foreach($resourceEdits as $edit)
                <li>
                    <a href="{{ $edit->edit_title }}">
                        <h2 class="text-2xl font-bold">{{ $edit->edit_title }}</h2>
                    </a>
                    <h2>{{ $edit->edit_description }}</h2>
                </li>
            @endforeach
        </ul>
    @endif
</div>
