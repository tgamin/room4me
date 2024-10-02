@php 
    $color = 'bg-orange-400';
    if(is_array($reservation) && $reservation['money']['isFullyPaid']) $color = 'bg-emerald-500';
    else{
        if($reservation->isPaid()) $color = 'bg-emerald-500';
        elseif($reservation->isCancelled()) $color = 'bg-red-500';
    }
    $text = __('Unpaid');
    if($color == 'bg-emerald-500') $text = __('Paid');
    elseif($color == 'bg-red-500') $text = __('Canceled');
@endphp

<span class="inline-flex mt-1 px-2 py-1 rounded-lg z-10 uppercase {{ $color }} text-sm font-medium text-white select-none">
    {{ $text }}
</span>