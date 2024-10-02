<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Services\WordpressService;
use App\Notifications\ReservationUnpaidNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUnpaidSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservation;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $wordpressService = new WordpressService();
            $translations = $wordpressService->getTranslations();
            $this->reservation->notify(new ReservationUnpaidNotification($this->reservation, $translations));
            $this->reservation->smsCount = $this->reservation->smsCount + 1;
            $this->reservation->save();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
