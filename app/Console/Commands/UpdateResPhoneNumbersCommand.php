<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GuestyService;
use App\Services\ReservationService;

class UpdateResPhoneNumbersCommand extends Command
{
    private $guestyService;
    private $reservationService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:res:phone-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update reservation phone numbers';

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
        $reservations = $this->reservationService->getByStatus('collected');
        foreach ($reservations as $reservation) {
            $guestyReservation = $this->guestyService->getReservation($reservation->idRes);
            $guest = $this->guestyService->getGuest($guestyReservation['guest']['_id']);
            dump($guest);
        }
    }
}
