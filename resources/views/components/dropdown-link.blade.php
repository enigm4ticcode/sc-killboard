@php
$baseStyle = 'color: rgb(var(--fg));';
$currentStyle = $attributes->get('style', '');
$mergedStyle = trim($baseStyle . ' ' . $currentStyle);
@endphp

<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-left text-sm leading-5 transition duration-150 ease-in-out'])->except('style') }} style="{{ $mergedStyle }}" onmouseover="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onmouseout="this.style.backgroundColor='transparent'" onfocus="this.style.backgroundColor='rgb(var(--dropdown-hover))'" onblur="this.style.backgroundColor='transparent'">{{ $slot }}</a>
