@props(['resource'])

<!-- Favorite Button with Star -->
<form method="POST"
    hx-post="{{ route('favorites.post', $resource->id) }}" 
    hx-target="this"
    hx-swap="outerHTML"
    >
    @csrf
    <button type="submit"> 
        &#9734; Favorite <!-- Empty Star -->
    </button>
</form>
