<x-layouts.app class="sm:w-full sm:max-w-5xl">
    @include('templates.reservation.steps')
    <div class="xl:w-11/12 flex flex-col md:flex-row mx-auto p-10 rounded bg-white">
        <div class="flex-1">
            <h1 class="text-3xl mb-5 font-bold">{{ __('Summary') }}</h1>
            <h2 class="flex items-center text-2xl my-5 font-semibold">
                {{ __('Your stay') }}
                <div class="ml-2">
                    @include('templates.reservation.status', ['reservation' => $reservation])
                </div>
            </h2>
            <ul class="grid grid-cols-3">
                <li>
                    <p><strong>{{ __('Dates') }}</strong></p>
                    <p class="text-gray-500">{{ date('d/m/Y', strtotime($reservation['dateCheckin'])) }} - {{ date('d/m/Y', strtotime($reservation['dateCheckout'])) }}</p>
                </li>
                <li>
                    <p><strong>{{ __('Nights') }}</strong></p>
                    <p class="text-gray-500">{{ $reservationObject['nightsCount'] ?? '' }}</p>
                </li>
                <li>
                    <p><strong>{{ __('Guests') }}</strong></p>
                    <p class="text-gray-500">{{ $reservationObject['guestsCount'] ?? '' }}</p>
                </li>
            </ul>
            <div class="mt-5">
                <div id="default-carousel" class="relative" data-carousel="static">
                    <!-- Carousel wrapper -->
                    <div class="overflow-hidden relative h-56 rounded-lg sm:h-64 xl:h-80 2xl:h-96">
                        @foreach($listingObject['pictures'] as $picture)
                            <div class="hidden duration-700 ease-in-out" data-carousel-item>
                                <img src="{{ $picture['original'] }}" alt="{{ $picture['caption'] ?? $listingObject['title'] }}" class="block absolute top-1/2 left-1/2 w-full -translate-x-1/2 -translate-y-1/2">
                            </div>
                        @endforeach
                    </div>
                    <!-- Slider controls -->
                    <button type="button" class="flex absolute top-0 left-0 z-30 justify-center items-center px-4 h-full cursor-pointer group focus:outline-none" data-carousel-prev>
                        <span class="inline-flex justify-center items-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                            <svg class="w-5 h-5 text-white sm:w-6 sm:h-6 dark:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            <span class="hidden">Previous</span>
                        </span>
                    </button>
                    <button type="button" class="flex absolute top-0 right-0 z-30 justify-center items-center px-4 h-full cursor-pointer group focus:outline-none" data-carousel-next>
                        <span class="inline-flex justify-center items-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                            <svg class="w-5 h-5 text-white sm:w-6 sm:h-6 dark:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            <span class="hidden">Next</span>
                        </span>
                    </button>
                </div>
            </div>
            @if(!empty($services))
                <h2 class="pt-4 flex items-center text-2xl my-5 font-semibold">{{ __('Complete your profil') }}</h2>
                @if(!empty($servicesCategories))
                    <div class="mb-2">
                        <ul class="flex flex-wrap -mb-px text-sm text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                            @foreach ($servicesCategories as $categoryId => $category)
                                @if(isset($services[$category['id']]))
                                    <li class="mr-2 mb-2" role="presentation">
                                        <button class="tab inline-block py-3 px-4 font-bold rounded-lg hover:text-gray-900 bg-gray-100 active:bg-emerald-600 active:text-white" data-tabs-target="#{{ $category['slug'] }}" type="button" role="tab" aria-controls="{{ $category['slug'] }}" aria-selected="false">{{ app()->getLocale() == 'en' ? $category['name'] : $category['description'] }}</button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div id="myTabContent">
                        @foreach ($servicesCategories as $i => $category)
                            <div class="hidden p-4 bg-gray-50 rounded-lg dark:bg-gray-800" id="{{ $category['slug'] }}" role="tabpanel" aria-labelledby="{{ $category['slug'] }}-tab">
                                <div id="accordion-collapse" data-accordion="collapse">
                                    @foreach ($services as $categoryId => $service)
                                        @if(isset($services[$categoryId]) && $categoryId == $category['id'])
                                            @foreach ($services[$categoryId] as $i => $service)
                                                @php
                                                    $postTitle = isset($service['service']['translation']) && isset($service['service']['translation'][app()->getLocale()]) ? $service['service']['translation'][app()->getLocale()]['title'] : $service['service']['post_title'];
                                                    $postDescription = isset($service['service']['translation']) && isset($service['service']['translation'][app()->getLocale()]) ? $service['service']['translation'][app()->getLocale()]['description'] : $service['service']['post_content'];
                                                @endphp
                                                <h2 id="accordion-collapse-heading-{{ $category['slug'] . '_' . $i }}">
                                                    <button type="button" class="flex justify-between items-center p-5 w-full font-medium text-left border  border-gray-200  dark:focus:ring-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white" data-accordion-target="#accordion-collapse-body-{{ $category['slug'] . '_' . $i }}" aria-expanded="true" aria-controls="accordion-collapse-body-{{ $category['slug'] . '_' . $i }}">
                                                        <div class="flex items-center">
                                                            <div>
                                                                <p>{{ $postTitle }}</p>
                                                                <x-price :amount="$service['amount']" />
                                                            </div>
                                                        </div>
                                                        <svg data-accordion-icon="" class="w-6 h-6 rotate-180 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                    </button>
                                                </h2>
                                                <div id="accordion-collapse-body-{{ $category['slug'] . '_' . $i }}" aria-labelledby="accordion-collapse-heading-{{ $category['slug'] . '_' . $i }}">
                                                    <div id="service-details-{{ $service['service']['ID'] }}" class="flex flex-col justify-between items-center p-5 border  border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                                                        <div class="mb-4 flex flex-col xl:flex-row">
                                                            @if($service['service']['image'])
                                                                <div class="flex-1 rounded w-full bg-cover bg-center" style="min-width:11rem;min-height:15rem;background-image: url('{{ $service['service']['image']['sizes']['medium'] }}')">
                                                                </div>
                                                            @endif
                                                            <div class="m-4 w-full xl:w-1/2">
                                                                <h3 class="font-bold">{{ $postTitle }}</h3>
                                                                <p class="my-2 text-gray-500">{!! strip_tags($postDescription, '<a><br>') !!}</p>
                                                            </div>
                                                        </div>
                                                        <div class=" flex justify-between md:justify-start ml-auto items-center">
                                                            <x-price :amount="$service['amount']" />
                                                            <form id="service-form-{{ $service['service']['ID'] }}" class="service-form ml-4 flex items-center" action="{{ route('cart-item.store') }}" method="POST">
                                                                @csrf
                                                                <input class="w-20 text-xs mr-2 border border-gray-300" type="number" name="quantity" value="1" />
                                                                <input name="reservationId" type="hidden" value="{{ $reservation->idRes }}" />
                                                                <input id="checking_time_hidden-{{ $service['service']['ID'] }}" name="checking_time" type="hidden" value="{{ $defaultCheckingTime }}" />
                                                                <input id="checkout_time_hidden-{{ $service['service']['ID'] }}" name="checkout_time" type="hidden" value="{{ $defaultCheckoutTime }}" />
                                                                <input name="post_id" type="hidden" value="{{ $service['service']['ID'] }}" />
                                                                <input name="title" type="hidden" value="{{ $postTitle }}" />
                                                                <input name="amount" type="hidden" value="{{ $service['amount'] }}" />
                                                                <x-button backgroundColor="red" type="submit" class="text-center justify-center font-extrabold">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                                    </svg>
                                                                    <span class="hidden md:visible">{{ __('Add to cart') }}</span>
                                                                </x-button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            <div id="modalEl" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
                <div class="relative w-full h-full max-w-2xl md:h-auto">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="flex items-start justify-between p-5 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 lg:text-2xl dark:text-white">
                                {{ __('Option required') }}
                            </h3>
                            <button id="close-modal-button" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div id="modal-body" class="p-6 space-y-6">
                           
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <x-button id="accept-modal-button" type="button" backgroundColor="red" type="submit" class="text-center justify-center font-extrabold">
                                {{ __('Add to cart') }}                                     
                            </x-button>
                            <button id="decline-modal-button" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">{{ __('No, thanks') }}</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @if(!$reservation->isCancelled())
            <form method="POST" novalidate action="{{ route('cart.checkout', $cart) }}" enctype="multipart/form-data" class="user-form md:w-4/12 mt-5 md:ml-10 md:mt-0">
                @csrf
                <div class="relative mx-auto w-full">
                    <div class="border border-gray-200 p-4 rounded-lg bg-white mb-4">
                        <div class="flex">
                            <div class="overflow-hidden transition-transform w-full rounded">
                                <img class="rounded duration-500 transform ease-in-out hover:scale-110 " src="{{ $listingObject['picture']['thumbnail'] }}" alt="{{ $listingObject['title'] }}">
                            </div>
                            <div class="ml-4">
                                <h3 class="font-bold">{{ $listingObject['title'] }}</h3>
                                <p class="my-2 text-gray-500">{{ $listingObject['address']['full'] }}</p>
                            </div>
                        </div>
                        <div class="my-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="checking_time">
                                {{ __('Checkin time') }}
                            </label>
                            <select id="checking_time" name="checking_time" required class="@error('checking_time') border-red-500 @enderror appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600">
                                <option value="">{{ __('Checkin time') }}</option>
                                @foreach ($checkingTimes as $checkingTime)
                                {{-- checkingTime this one isn't what we need to update --}}
                                    <option value="{{ $checkingTime }}" {{ $defaultCheckingTime == $checkingTime ? 'selected="selected"' : '' }}>{{ $checkingTime }}</option>
                                @endforeach
                            </select>
                            <p class="error mt-2 text-red-500 hidden @error('checking_time') block @enderror">{{ __('Please choose a checkin time') }}</p>
                        </div>
                        <div class="my-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="checking_time">
                                {{ __('Checkout time') }}
                            </label>
                            <select id="checkout_time" name="checkout_time" required class="@error('checkout_time') border-red-500 @enderror appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600">
                                <option value="">{{ __('Checkout time') }}</option>
                                @foreach ($checkoutTimes as $checkoutTime)
                                    <option value="{{ $checkoutTime }}" {{ $defaultCheckoutTime == $checkoutTime ? 'selected="selected"' : '' }}>{{ $checkoutTime }}</option>
                                @endforeach
                            </select>
                            <p class="error mt-2 text-red-500 hidden @error('email') block @enderror">{{ __('Please choose a checkout time') }}</p>
                        </div>
                    </div>
                    <div class="border border-gray-200 p-4 rounded-lg bg-white mb-4">
                        <div class="my-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="name">
                                {{ __('Name') }}
                            </label>
                            <input id="name" name="name" required value="{{ $currentReservation->name ?? '' }}" type="text" placeholder="{{ __('Name') }}" class="@error('name') border-red-500 @enderror appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600">
                            <p class="error mt-2 text-red-500 hidden @error('name') block @enderror">{{ __('This field is required') }}</p>
                        </div>
                        <div class="my-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="prename">
                                {{ __('Firstname') }}
                            </label>
                            <input id="prename" name="prename" required value="{{ $currentReservation->prename ?? '' }}" type="text" placeholder="{{ __('Firstname') }}" class="@error('prename') border-red-500 @enderror appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600">
                            <p class="error mt-2 text-red-500 hidden @error('prename') block @enderror">{{ __('This field is required') }}</p>
                        </div>
                        <div class="my-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="email">
                                {{ __('Email') }}
                            </label>
                            <input id="email" name="email" required value="{{ $currentReservation->email ?? '' }}" type="email" placeholder="{{ __('Email') }}" class="@error('email') border-red-500 @enderror appearance-none block w-full bg-gray-50 border {{ $currentReservation->email ? 'border-gray-300' : 'border-rose-500' }} text-gray-900 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600">
                            <p class="error mt-2 text-red-500 hidden @error('email') block @enderror">{{ __('Please enter a valid email') }}</p>
                        </div>
                        <div class="my-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="phone">
                                {{ __('Phone') }}
                            </label>
                            <input id="phone" name="phone" required value="{{ $currentReservation->phone ?? '' }}" type="text" placeholder="{{ __('Phone') }}" class="@error('phone') border-red-500 @enderror appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600">
                            <p class="error mt-2 text-red-500 hidden @error('phone') block @enderror">{{ __('This field is required') }}</p>
                        </div>
                        <div class="custom-number-input my-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="phone">
                                {{ __('Number of double bed') }}
                            </label>
                            <div class="flex flex-row h-10 w-full rounded-lg relative bg-transparent mt-1">
                                <a href="#" data-action="decrement" class="flex items-center justify-center bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-300 h-full w-20 rounded-l cursor-pointer outline-none">
                                    <span class="m-auto text-2xl font-thin">−</span>
                                </a>
                                <input id="double_beds" readonly name="double_beds" required value="{{ $currentReservation->double_beds ?? old('double_beds') }}" type="number" min="0" max="5" placeholder="{{ __('Number of double bed') }}" class="@error('double_beds') border-red-500 @enderror outline-none focus:outline-none focus:border-gray-300 focus:ring-0 text-center appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight">
                                <a href="#" data-action="increment" class="flex items-center justify-center bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-300 h-full w-20 rounded-r cursor-pointer">
                                    <span class="m-auto text-2xl font-thin">+</span>
                                </a>
                            </div>
                            <p class="error mt-2 text-red-500 hidden @error('double_beds') block @enderror">{{ __('This field is required') }}</p>
                        </div>
                        <div class="custom-number-input my-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="phone">
                                {{ __('Number of single bed') }}
                            </label>
                            <div class="flex flex-row h-10 w-full rounded-lg relative bg-transparent mt-1">
                                <a href="#" data-action="decrement" class="flex items-center justify-center bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-300 h-full w-20 rounded-l cursor-pointer outline-none">
                                    <span class="m-auto text-2xl font-thin">−</span>
                                </a>
                                <input id="single_beds" readonly name="single_beds" required value="{{ $currentReservation->single_beds ?? old('single_beds') }}" type="number" min="0" max="5" placeholder="{{ __('Number of single bed') }}" class="@error('single_beds') border-red-500 @enderror outline-none focus:outline-none focus:border-gray-300 focus:ring-0 text-center appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight">
                                <a href="#" data-action="increment" class="flex items-center justify-center bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-300 h-full w-20 rounded-r cursor-pointer">
                                    <span class="m-auto text-2xl font-thin">+</span>
                                </a>
                                <p class="error mt-2 text-red-500 hidden @error('single_beds') block @enderror">{{ __('This field is required') }}</p>
                            </div>
                            <p class="error mt-2 text-red-500 hidden @error('single_beds') block @enderror">{{ __('This field is required') }}</p>
                        </div>
                        <div class="mb-6">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="identity_document">
                                {{ __('Identity document') }}
                            </label>
                            <x-btn href="{{ $currentReservation->identity_document }}" target="_blank" backgroundColor="green" class="btn-identity-document {{ $currentReservation->identity_document ? 'block' : 'hidden' }} mb-4 rounded-xl text-center justify-center font-extrabold">
                                {{ __('View file') }}
                            </x-btn>
                            <input id="identity_document" name="identity_document" type="file" class="block mt-2">
                            <p class="error mt-2 text-red-500 hidden @error('identity document') block @enderror">{{ __('Your ID is required to validate your reservation') }}</p>
                        </div>
                    </div>
                    <div class="border border-gray-200 p-4 rounded-lg bg-white mb-4">
                        <div id="cart-accordion-collapse" data-accordion="collapse">
                            <h2 id="accordion-collapse-heading-history">
                                <button type="button" class="flex justify-between items-center px-5 w-full font-medium text-left border  border-gray-200 bg-white-100 hover:bg-gray-100 text-gray-900 " data-accordion-target="#accordion-collapse-body-history" aria-expanded="false" aria-controls="accordion-collapse-body-history">
                                    <div class="flex items-center my-5 font-semibold">{{ __('Order history') }}</div>
                                    <svg data-accordion-icon="" class="w-6 h-6 rotate-180 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </button>
                            </h2>
                            <div id="accordion-collapse-body-history" class="border border-gray-200 border-t-0 p-5" aria-labelledby="accordion-collapse-heading-cart">
                                <ul class="mb-3">
                                    @if(!empty($reservationObject['money']['invoiceItems']))
                                        @foreach($reservationObject['money']['invoiceItems'] as $invoiceItem)
                                            <li class="py-2 text-sm border-b border-gray-200 flex justify-between items-center">
                                                {{ __($invoiceItem['title']) }}                                          
                                                <x-price :amount="$invoiceItem['amount']" weight="bold" size="sm" />
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                            <h2 id="accordion-collapse-heading-cart">
                                <button type="button" class="flex justify-between items-center px-5 w-full font-medium text-left border  border-gray-200 bg-white-100 hover:bg-gray-100 text-gray-900 " data-accordion-target="#accordion-collapse-body-cart" aria-expanded="true" aria-controls="accordion-collapse-body-cart">
                                    <div class="flex items-center my-5 font-semibold">{{ __('Cart') }}</div>
                                    <svg data-accordion-icon="" class="w-6 h-6 rotate-180 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </button>
                            </h2>
                            <div id="accordion-collapse-body-cart" class="border border-gray-200 border-t-0 p-5" aria-labelledby="accordion-collapse-heading-cart">
                                <ul class="mb-10">
                                    @php $hasCancellationInsurance = false; @endphp
                                    @foreach($cartItems as $cartItem)
                                        @if($cartItem->title === __('Cancellation insurance'))
                                            @php $hasCancellationInsurance = true; @endphp
                                        @endif
                                        <li class="py-4 first:border-t border-b border-gray-200 flex justify-between items-center">
                                            <div class="flex items-center">
                                                <p>
                                                    {{ $cartItem->title }}
                                                    <span class="text-sm">x{{ $cartItem->quantity }}</span>
                                                </p>
                                                @if($cartItem->title != 'Reservation')
                                                    <a href="{{ route('cart-item.remove', $cartItem) }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mt-2 ml-2" viewBox="0 0 20 20" fill="#ef4444">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <x-price :amount="$cartItem->amount" weight="bold" />
                                                @if($cartItem->title == 'Reservation')
                                                    <div>
                                                        <span>{{ __('Including tax') }}</span>
                                                        <x-price :amount="$reservationObject['money']['totalTaxes']" size="sm" />
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                    @php 
                                        $hostPayout = $reservationObject['money']['hostPayout'];
                                        if($reservationObject['money']['balanceDue'] == 0 && isset($reservationObject['money']['hostOriginalPayout'])){
                                            $hostPayout = $reservationObject['money']['hostOriginalPayout'];
                                        }
                                        $hostPayoutTotal = $hostPayout - $reservationObject['money']['balanceDue'];
                                        $alreadyPaid = 0;
                                    @endphp
                                    <li class="py-4 first:border-t border-b border-gray-200 flex justify-between items-center">
                                        <strong>{{ __('Total amount') }}</strong>
                                        <x-price :amount="$cart->total() + $hostPayoutTotal" weight="bold" />
                                    </li>
                                    @if(!empty($reservationObject['money']['payments']))
                                        @foreach($reservationObject['money']['payments'] as $payment)
                                            @if($payment['status'] == 'SUCCEEDED')
                                                <li class="py-2 text-sm border-b border-gray-200 flex justify-between items-center">
                                                    {{ __('Payment performed on') }} {{ date('d/m/Y à H:i', strtotime($payment['paidAt'])) }}                                          
                                                    <x-price :amount="$payment['amount']" weight="bold" size="sm" />
                                                </li>
                                                @php $alreadyPaid += $payment['amount']; @endphp
                                            @endif
                                        @endforeach
                                        <li class="py-4 flex border-b border-gray-200 justify-between items-center">
                                            <strong>{{ __('Amount already paid') }}</strong>
                                            <x-price :amount="$alreadyPaid" weight="bold" />
                                        </li>
                                    @endif
                                    <li class="py-4 flex border-b border-gray-200 justify-between items-center">
                                        <strong>{{ __('Left to pay') }}</strong>
                                        <x-price :amount="$cart->total()" weight="bold" />
                                    </li>
                                </ul>
                            </div>

                            @if(!$hasCancellationInsurance)
                                <div class="mt-4 px-5 w-full border border-gray-200">
                                    <div class="flex justify-between items-center ">
                                        <div class="flex flex-col items-start py-4">
                                            <label for="cancellation_insurance" class="mb-2 block uppercase tracking-wide text-gray-700 text-xs font-bold">
                                                {{ __('Cancellation insurance') }}
                                            </label>
                                            <p class="text-sm mr-4 mb-2">{{ __('Cancellation insurance details') }}</p>
                                            <x-btn href="{{ route('cart.addCancellationInsurance', $cart->id) }}"  backgroundColor="green" class="rounded-xl mt-3 py-3 text-sm text-center justify-center font-extrabold">
                                                <span class="text-xs mr-2">{{ __('Add to cart') }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </x-btn>
                                        </div>
                                        <x-price :amount="$cancellationInsurancePrice" weight="bold" />
                                    </div>
                                </div>
                            @endif

                            <input name="idRes" type="hidden" value="{{ $reservation->idRes }}">
                            <x-button backgroundColor="red" class="my-5 rounded-xl py-3 w-full text-{{ $cart->total() > 0 ? 'lg' : 'sm' }} text-center justify-center font-extrabold">{{ $cart->total() > 0 ? __('Pay') : __('Update my information') }}</x-button>
                            <x-btn href="{{ $reservation->isCheckingInstructionsEnabled() ? route('reservation.confirmation', $reservation->confCode) : '#' }}" backgroundColor="{{ $reservation->isCheckingInstructionsEnabled() ? 'green' : 'gray' }}" class="inline-flex w-full text-sm py-3 rounded-xl text-center justify-center font-extrabold">
                                {{ __('Checking instructions') }}
                            </x-btn>
                        </div>
                    </a>
                </div>
                <input name="reservationId" type="hidden" value="{{ $reservation->idRes }}" />
            </form>
        @endif
    </div>


<script type="text/javascript">
    function decrement(e) {
        e.preventDefault();
        const btn = e.target.parentNode.parentElement.querySelector('a[data-action="decrement"]');
        const target = btn.nextElementSibling;
        let value = Number(target.value);
        if(value > parseInt(target.getAttribute('min'))){
            value--;
            target.value = value;
        }
    }

    function increment(e) {
        e.preventDefault();
        const btn = e.target.parentNode.parentElement.querySelector(
        'a[data-action="decrement"]'
        );
        const target = btn.nextElementSibling;
        let value = Number(target.value);
        if(value < parseInt(target.getAttribute('max'))){
            value++;
            target.value = value;
        }
    }

    const decrementButtons = document.querySelectorAll(
        `a[data-action="decrement"]`
    );

    const incrementButtons = document.querySelectorAll(
        `a[data-action="increment"]`
    );

    decrementButtons.forEach(btn => {
        btn.addEventListener("click", decrement);
    });

    incrementButtons.forEach(btn => {
        btn.addEventListener("click", increment);
    });
</script>
@yield('javascript')

</x-layouts.app>