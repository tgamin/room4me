<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GuestyService;
use App\Services\ReservationService;
use App\Services\WordpressService;

class UpdateResStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:res:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update res status';

    private $guestyService;
    private $reservationService;
    private $wordpressService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GuestyService $guestyService, ReservationService $reservationService, WordpressService $wordpressService)
    {
        parent::__construct();
        $this->guestyService = $guestyService;
        $this->reservationService = $reservationService;
        $this->wordpressService = $wordpressService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reservations = $this->reservationService->getFromToday();
        $options = $this->wordpressService->getOptions();
        $fields = 'status canceledAt money listing confirmationCode';
        foreach ($reservations as $res) {
            $reservation = $this->guestyService->getReservation($res->idRes, $fields);
            if($reservation){
                $option = [];
                if (isset($options['listings'])) {
                    foreach ($options['listings'] as $listing) {
                        if (isset($reservation['listingId']) && isset($listing['listingId']) && $reservation['listingId'] == $listing['listingId']) {
                            $option = $listing;
                        }
                    }
                }
                $debitCardBooking = $option['debit_card_booking'] ?? false;
                if (isset($reservation['canceledAt'])) {
                    $res->update(['statut' => 'cancelled']);
                    $this->info("Reservation " . $res->confCode . " statut updated to cancelled");
                } elseif (isset($reservation['money']) && $reservation['money']['isFullyPaid']) {
                    $res->update(['statut' => 'paid']);
                    $this->info("Reservation " . $res->confCode . " statut updated to paid");
                } elseif (isset($reservation['integration']) && $reservation['integration']['platform'] == 'bookingCom' && $debitCardBooking) {
                    $res->update(['debit_card_statut' => 'paid']);
                    $this->info("Reservation " . $res->confCode . " debit_card_statut updated to paid");
                } else {
                    $this->info("Reservation " . $res->confCode . " nothing to do");
                }
            }
        }
    }
}
