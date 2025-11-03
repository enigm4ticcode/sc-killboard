@props(['active'])

@php
$isActive = $active ?? false;
$classes = $isActive
            ? 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out opacity-70 hover:opacity-100';

$style = $isActive ? 'color: rgb(var(--accent)); border-color: rgb(var(--accent));' : 'color: rgb(var(--fg));';
@endphp

<a {{ $attributes->merge(['class' => $classes])->except('style') }} style="{{ $style }}">
    {{ $slot }}
</a>
