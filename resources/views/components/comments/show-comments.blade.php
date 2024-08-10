@props(['id', 'type'])

<div x-data="{ open: false }">
    <button x-on:click="open = ! open" 
    hx-get="{{ route('comment.comments', ['id' => $id, 'type' =>$type]) }}" hx-target="#comments-{{$id}}-{{$type}}"
    hx-indicator="#spinner-{{$id}}-{{$type}}"
    class="bg-teal-300 p-2 mt-2">
    View Comments
    </button>
    
    <div x-show="open" id="comments-{{$id}}-{{$type}}">  
        <x-spinner class="mx-auto" id="spinner-{{$id}}-{{$type}}"></x-spinner>
    </div>
</div>