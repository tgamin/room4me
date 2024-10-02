<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ReservationCheckinInstructions extends Notification
{
    use Queueable;

    private $reservation;
    private $lang;
    private $subject;
    private $body;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reservation, $body)
    {
        $this->reservation = $reservation;
        $this->lang = substr($reservation->phone, 0, 2) == '33' ? 'fr' : 'en';
        $this->subject = __('Your check-in instructions', [], $this->lang);
        $this->body = $body;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->subject($this->subject)
                ->line(new HtmlString($this->body))
                ->salutation(__('Regards', [], $this->lang));
    }
}
