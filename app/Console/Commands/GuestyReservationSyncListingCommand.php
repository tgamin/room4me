<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing;
use App\Models\Guest;
use App\Models\Reservation;
use App\Services\GuestyService;
use App\Services\ReservationService;

class GuestyReservationSyncListingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesty:reservation:sync:listing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync reservations with listings';

    private $guestyService;
    private $reservationService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GuestyService $guestyService, ReservationService $reservationService)
    {
        parent::__construct();
        $this->guestyService = $guestyService;
        $this->reservationService = $reservationService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //$i = 0;
        //while ($i < 422) {
            /*$confCodes = $this->reservationService->getConfCodesWithoutListingId($i);
            $reservations = $this->guestyService->getReservationsByConfCodes($confCodes);*/
            $reservations = $this->reservationService->getAllWithoutListingId();
            foreach ($reservations as $reservation) {
                $guestyReservation = $this->guestyService->getReservation($reservation->idRes);
                $listingId = $guestyReservation['listing']['_id'] ?? null;
                if($listingId){
                    $listing = Listing::where('listingId', $listingId)->first();
                    $reservation->update([
                        'listing_id' => $listing->id,
                        'idListing' => $listingId,
                        'object' => serialize($guestyReservation),
                    ]);
                    $this->info("Reservation " . $reservation->confCode . " synced");
                }else{
                    $this->info("Reservation " . $reservation->confCode . " has no listing");
                }

                /*$reservation = $this->reservationService->findByIdRes($guestyReservation['_id']);
                if($reservation && isset($guestyReservation['listing']['_id'])){
                    $listing = Listing::where('listingId', $guestyReservation['listing']['_id'])->first();
                    if($listing){
                        $reservation->update([
                            'listing_id' => $listing->id,
                            'idListing' => $guestyReservation['listing']['_id'],
                            'object' => serialize($guestyReservation),
                        ]);
                        $this->info("Reservation " . $reservation->confCode . " synced");
                    }
                }*/
            }
            //$i++;
        //}
    }
}
