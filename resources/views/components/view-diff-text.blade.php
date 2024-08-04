<!-- resources/views/components/view-diff-text.blade.php -->
@props(['diff'])

@php
    $diffArray = json_decode($diff, true);
@endphp

@foreach ($diffArray as $diffItem)
    @if (isset($diffItem['d']) && count($diffItem['d']) > 0)
        <span class="bg-red-200">-
            @foreach ($diffItem['d'] as $deletedText)
                {{ $deletedText }}
            @endforeach
        </span>
    @endif
    
    @if (isset($diffItem['i']) && count($diffItem['i']) > 0)
        <span class="bg-green-200">+
            @foreach ($diffItem['i'] as $insertedText)
                {{ $insertedText }}
            @endforeach
        </span>
    @endif
    
    @if (is_string($diffItem))
        <span>{{ $diffItem }}</span>
    @endif
@endforeach
