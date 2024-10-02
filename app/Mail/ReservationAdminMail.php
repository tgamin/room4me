<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Cart;
use App\Models\Reservation;

class ReservationAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cart;
    public $reservation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Cart $cart, Reservation $reservation)
    {
        $this->cart = $cart;
        $this->reservation = $reservation;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reservation.admin')->subject('Nouvelle commande');
    }
}
