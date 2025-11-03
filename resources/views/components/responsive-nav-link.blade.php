@props(['active'])

@php
$isActive = $active ?? false;
$classes = $isActive
            ? 'block w-full pl-3 pr-4 py-2 border-l-4 text-left text-base font-medium focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium focus:outline-none transition duration-150 ease-in-out';

$style = $isActive
    ? 'color: rgb(var(--accent)); border-color: rgb(var(--accent)); background-color: rgba(var(--accent), 0.05);'
    : 'color: rgb(var(--fg)); opacity: 0.7;';
@endphp

<a {{ $attributes->merge(['class' => $classes])->except('style') }}
   style="{{ $style }}"
   onmouseover="if (!this.style.borderColor.includes('var(--accent)')) { this.style.backgroundColor='rgba(var(--card-border), 0.3)'; this.style.opacity='1'; }"
   onmouseout="if (!this.style.borderColor.includes('var(--accent)')) { this.style.backgroundColor='transparent'; this.style.opacity='0.7'; }">
    {{ $slot }}
</a>
