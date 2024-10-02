@php
    $urlParams = isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    $currentReservation = session()->get('currentReservation');
@endphp
<div class="w-full py-6 px-6 bg-gray-900 text-gray-700 flex flex-col md:flex-row mx-auto items-center justify-between">
    <div class="flex-shrink-0 flex items-center w-32">
        <a href="{{ $currentReservation ? '/reservation/find?confCode=' . $currentReservation->confCode . '&dateCheckin=' . $currentReservation->dateCheckin . '' : '/' }}">
            <x-logo />
        </a>
    </div>
    <div class="flex flex-col md:flex-row items-center">
        @if($currentReservation)
            <div class="flex items-center mt-4 mx-2 md:mt-0">
                <p class="text-white mt-1">{{ __('Hello') }}, {{ $currentReservation->prename }} {{ $currentReservation->name }}</p>
                <a href="{{ route('reservation.edit', $currentReservation->idRes) }}" class="relative mr-0 md:mr-2 z-10 block p-2 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"  viewBox="0 0 20 20" fill="currentColor">
                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        @endif
        <div class="flex mt-4 md:mt-0">
            <x-language-switcher class="mr-3" />
            <a href="/" class="relative z-10 block  p-2 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </a>
        </div>
    </div>
</div>
