<form action="{{ route('reservation.find') }}">
  <div class="w-full p-7">
    <!--<div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-first-name">
        Nom
      </label>
      <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="grid-first-name" type="text" placeholder="Jane">
      <p class="text-red-500 text-xs italic">Please fill out this field.</p>
    </div>-->
    <div class="mb-6">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="confCode">
        {{ __('Name') }} {{ __('or') }} {{ __('Confirmation code') }} 
      </label>
      <input id="confCode" name="confCode" value="{{ old('confCode') }}" type="text" placeholder="{{ __('Name') }} {{ __('or') }} {{ __('Confirmation code') }} " class="@error('confCode') border border-red-500 @enderror appearance-none block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600">
    </div>
    <div class="mb-6">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="dateCheckin">
        {{ __('Checkin date') }}
      </label>
      <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
            </div>
            <input datepicker id="dateCheckin" value="{{ old('dateCheckin') }}" name="dateCheckin" type="text" datepicker-autohide datepicker-format="yyyy-mm-dd" placeholder="Selectionner une date" class="@error('dateCheckin') border border-red-500 @enderror block w-full bg-gray-50 border border-gray-300 text-gray-900 rounded py-3 px-4 pl-10 leading-tight focus:outline-none focus:bg-white focus:border-emerald-600 datepicker-input">
        </div>
    </div>
    <div class="ml-auto">
        <button class="w-full mr-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
            {{ __('View my reservations') }}
        </button>
    </div>
  </div>
</form>