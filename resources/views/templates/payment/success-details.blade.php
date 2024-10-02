@if($videos)
    <div class="px-4 mt-2">
        <h2 class="flex items-center text-2xl my-5 font-semibold">{{ __('Videos') }}</h2>
        <div class="px-4 mt-2">
            {!! $videos !!}
        </div>
    </div>
@endif  
@if($processBook)
    <div class="px-4 mt-2">
        <h2 class="flex items-center text-2xl my-5 font-semibold">{{ __('Process book') }}</h2>
        <div class="px-4 mt-2">
            {!! $processBook !!}
        </div>
    </div>
@endif  