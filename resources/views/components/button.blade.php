@props(['href' => false, 'backgroundColor' => 'emerald'])

<button {!! $href ? 'href="' . $href . '"' : '' !!} {{ $attributes->merge(['type' => 'submit', 'class' => "inline-flex items-center px-4 py-2 bg-$backgroundColor-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-$backgroundColor-700 active:bg-$backgroundColor-700 focus:outline-none focus:border-$backgroundColor-700 focus:ring ring-$backgroundColor-300 disabled:opacity-25 transition ease-in-out duration-150"]) }}>
    {{ $slot }}
</button>
