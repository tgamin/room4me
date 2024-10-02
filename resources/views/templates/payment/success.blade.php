<x-layouts.app class="sm:w-full sm:max-w-5xl">
    @php $currentReservation = session()->get('currentReservation'); @endphp
    @include('templates.reservation.steps', ['full' => true])
    @isset($display_confirmation)
        <div class="flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="text-3xl mb-5 font-bold">{{ __('Reservation accepted') }}</h1>
            <p class="mt-2 mb-10 text-gray-500 text-lg leading-4">{{ __('The payment has been accepted by your bank.') }}</p>
        </div>
    @endisset
    @if($currentReservation->isCheckingInstructionsEnabled() and isset($checkingInstructions))
        <div class="xl:w-11/12 flex flex-col md:flex-row mx-auto p-4 rounded bg-white">
            @if($videos or $processBook)
                <div class="md:hidden mt-5 informations">
                    @include('templates.payment.success-details')
                </div>  
            @endif  
            <div class="flex-1">
                <div class="px-4 mt-2">
                    <h2 class="flex items-center text-2xl my-5 font-semibold">{{ __('Checking instructions') }}</h2>
                    <p>{!! nl2br($checkingInstructions) !!}</p>
                </div>
            </div>
            @if($videos or $processBook)
                <div class="hidden md:flex md:flex-col md:flex-1 md:w-6/12 mt-5 md:ml-10 md:mt-0 informations">
                    @include('templates.payment.success-details')
                </div>  
            @endif  
        </div>
    @endif

    <div class="flex justify-center mt-5">
        <x-btn href="{{ $currentReservation ? '/reservation/find?confCode=' . $currentReservation->confCode . '&dateCheckin=' . $currentReservation->dateCheckin . '' : '/' }}" backgroundColor="green" class="rounded-xl py-3 text-sm text-center justify-center font-extrabold">
            {{ __('Back') }}
        </x-btn>
    </div>    
</x-layouts.app>

<style>
    .informations iframe, .informations .wp-video, .informations video{
        width:100% !important;
        max-width: 100%;
        margin: 1rem 0;
    }
    .informations a{
        text-decoration: underline;
        color: #0e9f6e;
    }
</style>