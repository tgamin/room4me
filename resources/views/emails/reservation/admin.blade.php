@component('mail::message')
<p>Bonjour,</p>
<p>
    La réservation du {{ date('d/m/Y', strtotime($reservation->dateCheckin)) }} au {{ date('d/m/Y', strtotime($reservation->dateCheckout)) }} 
    au nom de {{ $reservation->prename }}  {{ $reservation->name }} a été payée.
</p>
<p>Voici le détail de la transaction :</p>
<ul>
    
</ul>

@component('mail::table')
    | Produit       | Quantité      | Montant  |
    | :------------ |:-------------:| --------:|
    @foreach($cart->items as $item)
    | {{$item->title }} | x{{ $item->quantity }} | {{ number_format($item->amount, 2, ',', ' ') }} € |
    @endforeach
    | <b>Total</b>      |                        | <b>{{ number_format($cart->total(), 2, ',', ' ') }} €</b> |
@endcomponent

@endcomponent
