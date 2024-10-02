<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use \Illuminate\Notifications\Messages\NexmoMessage;

class ReservationUnpaidNotification extends Notification
{
    use Queueable;

    private $reservation;
    private $translations;
    private $lang;
    private $subject;
    private $message;
    private $payLink;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reservation, $translations)
    {
        $this->reservation = $reservation;
        $this->translations = $translations;
        $this->lang = substr($reservation->phone, 0, 2) == '33' ? 'fr' : 'en';
        $this->subject = $translations['reservation']['unpaid']['mail']['subject'][$this->lang];
        $this->buttonText = $translations['reservation']['unpaid']['mail']['button'][$this->lang];
        $format = $this->lang == 'fr' ? 'd/m/Y' : 'm/d/Y';
        $this->body = $translations['reservation']['unpaid']['mail']['body'][$this->lang];
        $this->body = str_replace('[PRENOM]', $this->reservation->prename, $this->body);
        $this->body = str_replace('[NOM]', $this->reservation->name, $this->body);
        $this->body = str_replace('[DATECHECKING]', date($format, strtotime($this->reservation->dateCheckin)), $this->body);
        $this->payLink = route('reservation.pay', $this->reservation->confCode);
        $template = $translations['reservation']['unpaid']['sms']['message'][$this->lang];
        if (strrpos($template, '[LIEN_PAIEMENT]') !== false) {
            $this->message = str_replace('[LIEN_PAIEMENT]', $this->payLink, $template);
        } elseif (strrpos($template, '[LINK_PAYMENT]') !== false) {
            $this->message = str_replace('[LINK_PAYMENT]', $this->payLink, $template);
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'nexmo'];
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
                ->line(new HtmlString('<a href="' . $this->payLink . '">' . $this->payLink . '</a>'))
                ->salutation(__('Regards', [], $this->lang));
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
            ->content($this->message)
            ->unicode();
    }
}
