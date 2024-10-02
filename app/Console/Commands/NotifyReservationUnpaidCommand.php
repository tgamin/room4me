<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReservationService;
use App\Jobs\SendUnpaidSms;

class NotifyReservationUnpaidCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:reservation:unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify guests unpaid reservation by sms and email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ReservationService $reservationService)
    {
        parent::__construct();
        $this->reservationService = $reservationService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reservations = $this->reservationService->getByStatus('collected');
        foreach ($reservations as $reservation) {
            if($reservation['debit_card_statut'] != 'paid'){
                dispatch(new SendUnpaidSms($reservation));
            }
        }
    }
}
