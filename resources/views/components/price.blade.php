@props(['amount' => 0, 'weight' => 'normal', 'size' => 'lg'])

<p class="inline-block font-{{ $weight }} text-primary whitespace-nowrap leading-tight rounded-xl">
    <span class="font-bold text-{{ $size }}">{{ number_format($amount, 2, ',', ' ') }}</span>
    <span>€</span>
</p>