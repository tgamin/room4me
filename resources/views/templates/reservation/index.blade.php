<x-layouts.app class="sm:w-full sm:max-w-5xl">
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="reservation-list w-full text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr class="hidden lg:table-row text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <th class="font-bold text-sm text-left p-6">{{ __('Reservation') }}</th>
                <th class="font-bold text-sm text-left p-6">{{ __('Checkin date') }}</th>
                <th class="font-bold text-sm text-left p-6">{{ __('Checkout date') }}</th>
                <th class="font-bold text-sm text-left p-6">{{ __('Status') }}</th>
            </tr>
            @foreach ($reservations as $reservation)
                <tr class="grid lg:table-row border border-gray-200 pt-4 px-2 mb-4 bg-white lg:p-0 lg:m-0 lg:border-0 lg:bg-transparent">
                    <td class="relative">
                        <p class="block lg:hidden font-bold text-sm px-6 m-0">
                            {{ __('Reservation') }}
                        </p>
                        <p scope="row" class="px-6 pb-4 font-bold text-gray-900 dark:text-white whitespace-nowrap">
                            {{ $reservation->confCode }}
                        </p>
                        <svg data-accordion-icon="" class="accordion-icon block lg:hidden w-6 h-6 absolute right-0 top-3 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </td>
                    <td class="hidden lg:table-cell">
                        <p class="block lg:hidden font-bold text-sm px-6 m-0">
                            {{ __('Checkin date') }}
                        </p>
                        <p class="px-6 pb-4">
                            {{ $reservation->dateCheckin }}
                        </p>
                    </td>
                    <td class="hidden lg:table-cell">
                        <p class="block lg:hidden font-bold text-sm px-6 m-0">
                            {{ __('Checkout date') }}
                        </p>
                        <p class="px-6 pb-4">
                            {{ $reservation->dateCheckout }}
                        </p>
                    </td>
                    <td class="hidden lg:table-cell">
                        <p class="block lg:hidden font-bold text-sm px-6 m-0">
                            {{ __('Status') }}
                        </p>
                        <div class="flex items-start px-6 pb-4">
                            @include('templates.reservation.status', ['reservation' => $reservation])
                        </div>
                    </td>
                    <td class="hidden lg:table-cell">
                        <div class="grid lg:flex">
                                @if(!$reservation->isCancelled())
                                    <p class="px-6 pb-4 text-center lg:text-right">
                                        <x-btn href="{{ route('reservation.pay', $reservation->confCode) }}" backgroundColor="{{ $reservation->isProfileCompleted() ? 'green' : 'red' }}" class="rounded-xl text-center justify-center font-extrabold">
                                            {{ $reservation->isPaid() ? __('Complete your profil') : __('Pay') }}
                                        </x-btn>
                                    </p>
                                @endif
                                <p class="px-6 pb-4 text-center lg:text-right">
                                    <x-btn href="{{ $reservation->isCheckingInstructionsEnabled() ? route('reservation.confirmation', $reservation->confCode) : '#' }}" backgroundColor="{{ $reservation->isCheckingInstructionsEnabled() ? 'green' : 'gray' }}" class="rounded-xl text-center justify-center font-extrabold">
                                        {{ __('Checking instructions') }}
                                    </x-btn>
                                </p>
                                <?php /* 
                                @if($reservation)
                                    <p class="px-6 pb-4 text-center lg:text-right">
                                        <x-btn href="{{ route('reservation.edit', $reservation->idRes) }}" backgroundColor="blue" class="rounded-xl text-center justify-center font-extrabold">
                                            {{ __('Identity document') }}
                                        </x-btn>
                                    </p>
                                @endif 
                                */ ?>
                            </div>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</x-layouts.app>