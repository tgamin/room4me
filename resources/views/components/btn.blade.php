@props(['href' => false, 'backgroundColor' => 'red'])
@php
    $bgColor = "bg-$backgroundColor-500";   
    $bgColorActive = "bg-$backgroundColor-700";   
@endphp

<a {!! $href ? 'href="' . $href . '"' : '' !!} {{ $attributes->merge(['class' => "inline-flex items-center px-4 py-2 $bgColor border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:$bgColorActive active:$bgColorActive focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"]) }}>
    {{ $slot }}
</a>

<div class="hidden bg-gray-500"></div>
