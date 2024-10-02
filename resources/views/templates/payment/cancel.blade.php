<x-layouts.app class="sm:w-full sm:max-w-5xl">
    @php $currentReservation = session()->get('currentReservation'); @endphp
    @include('templates.reservation.steps')
    <div class="xl:w-11/12 flex flex-col mx-auto p-10 rounded bg-white items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
        </svg>
        <h1 class="text-3xl mb-5 font-bold">{{ __('Payment has been canceled') }}</h1>
        <p class="my-2 text-gray-500 text-lg leading-4">{{ __('The payment has been canceled.') }}</p>
        
        @if($currentReservation)
            <div class="flex justify-center mt-5">
                <x-btn href="{{ route('reservation.pay', $currentReservation->confCode) }}" backgroundColor="orange" class="rounded-xl py-3 text-sm text-center justify-center font-extrabold">
                    {{ __('Back') }}
                </x-btn>
            </div>
        @endif
    </div>
</x-layouts.app>