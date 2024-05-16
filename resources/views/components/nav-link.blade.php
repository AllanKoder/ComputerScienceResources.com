@props(['active'])

@php
$style = 'inline-flex items-center mt-2 p-2 text-sm font-medium leading-5';

$classes = ($active ?? false)
            ? "$style border-x-2 border-t-2 border-gray rounded-t-lg bg-white text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 transition duration-150 ease-in-out"
            : "$style text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300";
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
