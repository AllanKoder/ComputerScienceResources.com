@props(['attributes' => []])

<div {{ $attributes->merge(['class' => 'htmx-indicator animate-spin border-4 border-t-4 border-gray-200 border-t-blue-500 rounded-full w-16 h-16']) }}></div>
