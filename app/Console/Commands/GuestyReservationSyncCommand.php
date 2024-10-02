<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing;
use App\Models\Guest;
use App\Models\Reservation;
use App\Services\GuestyService;
use App\Services\ReservationService;
use App\Services\WordpressService;

class GuestyReservationSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesty:reservation:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync reservations';

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
        $guestyReservations = $this->guestyService->getReservationsBeetween('2023-05-23');
        $options = $this->wordpressService->getOptions();
        foreach ($guestyReservations as $guestyReservation) {
            $statuses  = ['collected', 'out', 'paid'];
            $status = $statuses[0];
            $platform = $guestyReservation['integration']['platform'];
            if ($guestyReservation['money']['isFullyPaid'] || $platform == 'airbnb' || $platform == 'airbnb2' || $platform == 'tripAdvisor') {
                $status = $statuses[2];
            }
            $guestyGuest = $guestyReservation['guest'];
            $data = [
                'guestId' => $guestyGuest['_id'],
                'firstName' => $guestyGuest['firstName'] ?? '',
                'lastName' => $guestyGuest['lastName'] ?? '',
                'fullName' => $guestyGuest['fullName'] ?? '',
                'email' => $guestyGuest['email'] ?? '',
                'phone' => $guestyGuest['phone'] ?? '',
                'object' => serialize($guestyGuest),
            ];
            $guest = Guest::where('guestId', $guestyGuest['_id'])->first();
            if (!isset($guest)) {
                $guest = Guest::create($data);
            } else {
                $guest->update($data);
            }
            $listing = Listing::where('listingId', $guestyReservation['listingId'])->first();

            $data = [
                'idRes' => $guestyReservation['_id'],
                'listing_id' => $listing->id ?? null,
                'guest_id' => $guest->id ?? null,
                'idGuest' => $guestyReservation['guestId'],
                'dateCheckin' => (new \DateTimeImmutable($guestyReservation['checkIn']))->format('Y-m-d h:m:s'),
                'dateCheckout' => (new \DateTimeImmutable($guestyReservation['checkOut']))->format('Y-m-d h:m:s'),
                'confCode' => $guestyReservation['confirmationCode'] ?? null,
                'amount' => $guestyReservation['money']['hostPayout'],
                'statut' => $status,
                'platform' => $platform,
                'nombre_personne' => $guestyReservation['guestsCount'] ?? 0,
                'date_ajout' => (new \DateTimeImmutable($guestyReservation['createdAt']))->format('Y-m-d h:m:s'),
                'object' => serialize($guestyReservation),
            ];

            $listingObject = $listing->getObject();
            $data['nombre_lits'] = $listing['beds'] ? intval($listingObject['beds']) : 0;
            $data['name_adress'] = isset($listingObject['address']) ? $listingObject['address']['full'] : '';

            $option = [];
            if (isset($options['listings'])) {
                foreach ($options['listings'] as $listing) {
                    if (isset($guestyReservation['listingId']) && isset($listing['listingId']) && $guestyReservation['listingId'] == $listing['listingId']) {
                        $option = $listing;
                    }
                }
            }
            $debitCardBooking = $option['debit_card_booking'] ?? false;
            if (isset($guestyReservation['status']) && $guestyReservation['status'] == 'canceled') {
                $data['statut'] = 'cancelled';
            } elseif (isset($guestyReservation['money']) && $guestyReservation['money']['isFullyPaid']) {
                $data['statut'] = 'paid';
            } elseif (isset($guestyReservation['integration']) && $guestyReservation['integration']['platform'] == 'bookingCom' && $debitCardBooking) {
                $data['debit_card_statut'] = 'paid';
            }

            $reservation = $this->reservationService->findByIdRes($guestyReservation['_id']);
            if ($guest) {
                $fullName = $guest['lastName'] ?? $guest['fullName'];
            }
            if (!isset($reservation)) {
                if ($guest) {
                    $data['email'] = $guest['email'] ?? '';
                    $data['phone'] = $guest['phone'] ?? '';
                    $data['name'] = $fullName;
                    $data['prename'] = $guest['firstName'] ?? '';
                }
                $reservation = Reservation::create($data);
                $this->info("Reservation " . $reservation->confCode . " created");
            } else {
                if($guest){
                    if (strlen($reservation->email) == 0) {
                        $data['email'] = $guest['email'] ?? '';
                    }
                    if (strlen($reservation->phone) == 0) {
                        $data['phone'] = $guest['phone'] ?? '';
                    }
                    $data['name'] = $fullName;
                    if (strlen($reservation->prename) == 0) {
                        $data['prename'] = $guest['firstName'] ?? '';
                    }
                }
                $reservation->update($data);
                $this->info("Reservation " . $reservation->confCode . " updated");
            }
        }
    }
}
