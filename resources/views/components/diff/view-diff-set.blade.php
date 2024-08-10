<!-- resources/views/components/view-set-diff.blade.php -->
@props(['diff'])

@php
    $diffArray = json_decode($diff, true);
@endphp

<!-- Same -->
@if (isset($diffArray['s']) && count($diffArray['s']) > 0)
    <div>
        <span>{{ implode(', ', $diffArray['s']) }}</span>
    </div>
@endif

<!-- Insertions -->
@if (isset($diffArray['i']) && count($diffArray['i']) > 0)
    <div>
        <p class=" text-green-600">Insertions:</p>
        <span class="bg-green-200">{{ implode(', ', $diffArray['i']) }}</span>
    </div>
@endif

<!-- Deletions -->
@if (isset($diffArray['d']) && count($diffArray['d']) > 0)
    <div>
        <p class=" text-red-600">Deletions:</p>
        <span class="bg-red-200">{{ implode(', ', $diffArray['d']) }}</span>
    </div>
@endif

