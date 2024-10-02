<div class="w-full mb-5">
  <div class="flex">
    <div class="w-1/3">
      <div class="relative mb-2">
        <div class="w-10 h-10 mx-auto bg-green-500 rounded-full text-lg text-white flex items-center">
          <span class="text-center text-white w-full">
            <svg class="w-full fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
              <path class="heroicon-ui" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2zm14 8V5H5v6h14zm0 2H5v6h14v-6zM8 9a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm0 8a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
          </span>
        </div>
      </div>

      <div class="text-xs text-center md:text-base">{{ __('Summary') }}</div>
    </div>

    <div class="w-1/3">
      <div class="relative mb-2">
        <div class="absolute flex align-center items-center align-middle content-center" style="width: calc(100% - 2.5rem - 1rem); top: 50%; transform: translate(-50%, -50%)">
          <div class="w-full bg-gray-200 rounded items-center align-middle align-center flex-1">
            <div class="w-0 bg-green-300 py-1 rounded" style="width: 100%;"></div>
          </div>
        </div>

        <div class="w-10 h-10 mx-auto bg-green-500 rounded-full text-lg text-white flex items-center">
          <span class="text-center text-white w-full">
            <svg class="w-11 mt-1 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
            </svg>
          </span>
        </div>
      </div>

      <div class="text-xs text-center md:text-base">{{ __('Payment') }}</div>
    </div>

    <div class="w-1/3">
      <div class="relative mb-2">
        <div class="absolute flex align-center items-center align-middle content-center" style="width: calc(100% - 2.5rem - 1rem); top: 50%; transform: translate(-50%, -50%)">
          <div class="w-full bg-gray-200 rounded items-center align-middle align-center flex-1">
            <div class="w-0 bg-green-300 py-1 rounded" style="width: {{ isset($full) && $full ? '100' : '0' }}%;"></div>
          </div>
        </div>

        <div class="w-10 h-10 mx-auto {{ isset($full) && $full ? 'bg-green-500 border-0' : 'bg-white border-2' }}  border-gray-200 rounded-full text-lg text-white flex items-center">
          <span class="text-center {{ isset($full) && $full ? 'text-white' : 'text-gray-600' }} w-full">
            <svg class="w-full fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
              <path class="heroicon-ui" d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"/>
            </svg>
          </span>
        </div>
      </div>

      <div class="text-xs text-center md:text-base">{{ __('Confirmation') }}</div>
    </div>
  </div>
</div>