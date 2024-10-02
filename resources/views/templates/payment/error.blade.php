<x-layouts.app class="sm:w-full sm:max-w-5xl">
    @php $currentReservation = session()->get('currentReservation'); @endphp
    @include('templates.reservation.steps')
    <div class="xl:w-11/12 flex flex-col mx-auto p-10 rounded bg-white items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h1 class="text-3xl mb-5 font-bold">{{ __('Payment has been declined') }}</h1>
        <p class="my-2 text-gray-500 text-lg leading-4">{{ __('The payment has been refused by your bank. Please verify the credit card information entered.') }}</p>
        
        @if($currentReservation)
            <div class="flex justify-center mt-5">
                <x-btn href="{{ route('reservation.pay', $currentReservation->confCode) }}" backgroundColor="red" class="rounded-xl py-3 text-sm text-center justify-center font-extrabold">
                    {{ __('Back') }}
                </x-btn>
            </div>
        @endif
    </div>
</x-layouts.app>