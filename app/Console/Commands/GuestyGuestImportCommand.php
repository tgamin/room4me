<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Guest;
use App\Services\GuestyService;
use App\Services\GuestService;

class GuestyGuestImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesty:guest:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import guesty guests';

    private $guestyService;
    private $guestService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GuestyService $guestyService, GuestService $guestService)
    {
        parent::__construct();
        $this->guestyService = $guestyService;
        $this->guestService = $guestService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $guests = $this->guestyService->getGuests();
        foreach ($guests as $guestyGuest) {
            $data = [
                'guestId' => $guestyGuest['id'],
                'fullName' => $guestyGuest['fullName']['children'] ?? '',
                'firstName' => $guestyGuest['firstName']['children'] ?? '',
                'lastName' => $guestyGuest['lastName']['children'] ?? '',
                'email' => $guestyGuest['guestEmail']['children'] ?? '',
                'phone' => $guestyGuest['guestPhone']['children'] ?? '',
                'object' => serialize($guestyGuest),
            ];
            $guest = Guest::where('guestId', $guestyGuest['id'])->first();
            if (!isset($guest)) {
                $guest = Guest::create($data);
            } else {
                $guest->update($data);
            }
            $this->info("Guest " . $guestyGuest['id'] . " imported");
        }
    }
}
