<x-layouts.app class="sm:w-full sm:max-w-5xl">
    @if($currentReservation)
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <h1 class="text-3xl mb-5 font-bold">{{ __('My account') }}</h1>
            <form method="POST" action="{{ route('reservation.update', $currentReservation->idRes) }}" enctype="multipart/form-data"> 
                @csrf
                <div class="w-full p-7">
                    <div class="mb-6">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="name">
                            {{ __('Name') }}
                        </label>
                        <input id="name" name="name" value="{{ $currentReservation->name ?? '' }}" type="text" placeholder="{{ __('Name') }}" class="appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600">
                    </div>
                    <div class="mb-6">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="prename">
                            {{ __('Firstname') }}
                        </label>
                        <input id="prename" name="prename" value="{{ $currentReservation->prename ?? '' }}" type="text" placeholder="{{ __('Firstname') }}" class="appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600">
                    </div>
                    <div class="mb-6">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="email">
                            {{ __('Email') }}
                        </label>
                        <input id="email" name="email" value="{{ $currentReservation->email ?? '' }}" type="email" placeholder="{{ __('Email') }}" class="appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600">
                    </div>
                    <div class="mb-6">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="phone">
                            {{ __('Phone') }}
                        </label>
                        <input id="phone" name="phone" value="{{ $currentReservation->phone ?? '' }}" type="text" placeholder="{{ __('Phone') }}" class="appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600">
                    </div>
                    <div class="mb-6">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="identity_document">
                            {{ __('Identity document') }}
                        </label>
                        @if($currentReservation->identity_document)
                            <x-btn href="{{ $currentReservation->identity_document }}" target="_blank" backgroundColor="green" class="block mb-4 rounded-xl text-center justify-center font-extrabold">
                                {{ __('View file') }}
                            </x-btn>
                        @endif
                        <input id="identity_document" name="identity_document" type="file" class="block mt-2">
                    </div>
                    <div class="ml-auto">
                        <button class="w-full mr-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                            {{ __('Update') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif
</x-layouts.app>