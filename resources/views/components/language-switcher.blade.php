<div id="language-switcher" {{ $attributes->merge(['class' => '']) }}>
    @php
    $available_locales = ['fr' => 'FR', 'en' => 'EN'];
    @endphp
    <button id="dropdownDefault" data-dropdown-toggle="dropdown" class="text-white bg-gray-700 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800" type="button">
        <img class="mr-2" width="20" src="/img/{!! app()->getLocale() !!}.svg" alt="{!! $available_locales[app()->getLocale()] !!}" />
        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>
    <!-- Dropdown menu -->
    <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded shadow w-20 dark:bg-gray-700">
        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefault">
            @foreach($available_locales as $available_locale => $locale_name)
                @if($available_locale !== app()->getLocale())
                    <li>
                        <a href="{!! $available_locale === app()->getLocale() ? '#' : "/language/$available_locale" !!}" class="flex px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                            <img class="mr-2" width="20" src="/img/{!! $available_locale !!}.svg" alt="{!! $locale_name !!}" />
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>