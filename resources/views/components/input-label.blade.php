@props(['value'])

@php
$baseStyle = $attributes->get('style', '');
@endphp

<label {{ $attributes->merge(['class' => 'block font-medium text-sm'])->except('style') }} style="{{ $baseStyle }}">
    {{ $value ?? $slot }}
</label>
