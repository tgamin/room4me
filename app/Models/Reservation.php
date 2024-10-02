<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Reservation extends Model
{
    use HasFactory, Notifiable;

    public $guarded = [];

    /**
     * Get listing
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function isPaid() {
        return $this->statut == 'paid' || $this->debit_card_statut == 'paid' || $this->statut == 'success';
    }

    public function isProfileCompleted() {
        return $this->name != '' 
            && $this->prename != '' 
            && $this->email != ''
            && $this->phone != ''
            && strrpos($this->email, 'guest.booking.com') !== false;
    }

    public function isComingFromBooking() {
        return $this->platform == "bookingCom";
    }

    public function isComingFromBookingToday() {
        return $this->isComingFromBooking() && $this->dateCheckin == date('Y-m-d');
    }

    public function isCheckingInstructionsEnabled() {
        return $this->is_validated && date('Y-m-d', strtotime($this->dateCheckout)) >= date('Y-m-d');
        /*return $this->isPaid() 
            && $this->email != '' 
            && $this->checking_time != '' 
            && $this->checkout_time != ''
            && date('Y-m-d') <= date('Y-m-d', strtotime($this->dateCheckout . ' +1 day'))
            && !$this->isComingFromBookingToday();*/
    }

    public function isCancelled() {
        return $this->statut == 'cancelled';
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForMail($notification)
    {
        return [$this->email => $this->name];
    }
    
    /**
     * Route notifications for the Nexmo channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForNexmo($notification)
    {
        return $this->phone > 0 ? (string) $this->phone : null;
    }

    public function getObject(){
        return $this->object ? unserialize($this->object) : [];
    }
}
