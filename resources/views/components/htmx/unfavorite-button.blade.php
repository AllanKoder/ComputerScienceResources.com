@props(['resource'])

<!-- Unfavorite Button with Star -->
<form method="POST" 
    hx-delete="{{ route('favorites.destroy', $resource->id) }}" 
    hx-target"this"
    hx-swap="outerHTML"
    >
    @csrf
    <button type="submit"> 
        &#9733; Unfavorite <!-- Filled Star -->
    </button>
</form>
