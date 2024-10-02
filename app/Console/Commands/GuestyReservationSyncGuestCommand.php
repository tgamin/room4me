<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Guest;
use App\Models\Reservation;
use App\Services\GuestyService;
use App\Services\ReservationService;

class GuestyReservationSyncGuestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesty:reservation:sync:guest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync reservations with guests';

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
        $reservations = $this->reservationService->getAll();
        foreach ($reservations as $reservation) {
            if($reservation){
                $guest = Guest::where('guestId', $reservation->idGuest)->first();
                if($guest){
                    $reservation->update([
                        'guest_id' => $guest->id,
                    ]);
                    $this->info("Reservation guest " . $reservation->confCode . " synced");
                }
            }
        }
    }
}
