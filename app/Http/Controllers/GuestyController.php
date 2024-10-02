<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Listing;
use Illuminate\Http\Request;
use App\Services\GuestyService;
use App\Services\GuestService;
use App\Services\ListingService;
use App\Services\WordpressService;
use Illuminate\Support\Facades\Log;
use App\Services\ReservationService;

class GuestyController extends Controller
{
    private $guestyService;
    private $guestService;
    private $listingService;
    private $reservationService;
    private $wordpressService;

    public function __construct(GuestyService $guestyService, GuestService $guestService, ListingService $listingService, ReservationService $reservationService, WordpressService $wordpressService)
    {
        $this->guestyService = $guestyService;
        $this->guestService = $guestService;
        $this->listingService = $listingService;
        $this->reservationService = $reservationService;
        $this->wordpressService = $wordpressService;
    }

    public function addWebhooks(Request $request)
    {
        $webhooks = $this->guestyService->getWebhooks();

        $webhookSite = env('GUESTY_WEBHOOK_URL');
        if($webhookSite){
            foreach ($webhooks as $webhook) {
                if (isset($webhook['url'])) {
                    if (strrpos($webhook['url'], 'webhook.site') !== false) {
                        $this->guestyService->updateWebhook($webhook['_id'], $webhookSite, [
                            'reservation.new',
                            'reservation.updated',
                            'listing.new',
                            'listing.updated',
                            'guest.created',
                            'guest.updated',
                        ]);
                    }
                }
            }
        }

        dd($webhooks);

        // Delete webhooks
        /*$webhooks = $this->guestyService->getWebhooks();
        foreach ($webhooks as $webhook) {
            if(strrpos($webhook['url'], 'room4me.fr') !== false || strrpos($webhook['url'], 'webhook.site') !== false){
                $t = $this->guestyService->deleteWebhook($webhook['_id']);
            }
        }
        $webhooks = $this->guestyService->getWebhooks();

        // Reservation webhook
        $reservationUrls = [
            'https://preprod.room4me.fr/guesty/webhook/reservation',
            'https://app.room4me.fr/guesty/webhook/reservation',
        ];
        foreach ($reservationUrls as $url) {
            $this->guestyService->addWebhook($url, ['reservation.new', 'reservation.updated']);
        }
        
        // Listing webhook
        $listingUrls = [
            'https://preprod.room4me.fr/guesty/webhook/listing',
            'https://app.room4me.fr/guesty/webhook/listing',
        ];
        foreach ($listingUrls as $url) {
            $this->guestyService->addWebhook($url, ['listing.new', 'listing.updated']);
        }
        
        // Guest webhook
        $guestUrls = [
            'https://preprod.room4me.fr/guesty/webhook/guest',
            'https://app.room4me.fr/guesty/webhook/guest',
        ];
        foreach ($guestUrls as $url) {
            $this->guestyService->addWebhook($url, ['guest.created', 'guest.updated']);
        }

        $webhooks = $this->guestyService->getWebhooks();
        dd($webhooks);
        */
    }

    public function reservationWebhook(Request $request)
    {
        $inputs = $request->all();
        if (!isset($inputs['reservation']) || empty($inputs['reservation'])) {
            return response()->json(['errors' => ['reservation' => [__('Input reservation missing')]]], 422);
        }
        /*$guestyReservation = $this->guestyService->getReservation($inputs['reservation']['_id']);
        dd($guestyReservation);*/
        $guestyReservation = $inputs['reservation'];
        Log::info('guesty webhook reservation : ' . json_encode($inputs, JSON_UNESCAPED_SLASHES));
        $options = $this->wordpressService->getOptions();
        if ($guestyReservation && isset($guestyReservation['confirmationCode']) && $guestyReservation['confirmationCode']) {
            $statuses  = ['collected', 'out', 'paid'];
            $status = $statuses[0];
            $platform = $guestyReservation['integration']['platform'];
            if ($guestyReservation['money']['isFullyPaid'] || $platform == 'airbnb' || $platform == 'airbnb2' || $platform == 'tripAdvisor') {
                $status = $statuses[2];
            }
            $guest = Guest::where('guestId', $guestyReservation['guestId'])->first();
            if (!$guest) {
                $guestyGuest = $this->guestyService->getGuest($guestyReservation['guestId']);
                if(isset($guestyGuest['_id'])){
                    $guest = $this->guestService->createOrUpdate($guestyGuest);
                }
            }
            $listing = Listing::where('listingId', $guestyReservation['listingId'])->first();
            if (!$listing) {
                $guestyListing = $this->guestyService->getListing($guestyReservation['listingId']);
                if (isset($guestyListing['_id'])) {
                    $listing = $this->listingService->createOrUpdate($guestyListing);
                }
            }

            $data = [
                'idRes' => $guestyReservation['_id'],
                'listing_id' => $listing->id ?? null,
                'guest_id' => $guest->id ?? null,
                'idGuest' => $guestyReservation['guestId'],
                'confCode' => $guestyReservation['confirmationCode'],
                'amount' => $guestyReservation['money']['hostPayout'],
                'statut' => $status,
                'platform' => $platform,
                'nombre_personne' => $guestyReservation['guestsCount'] ?? 0,
                'date_ajout' => (new \DateTimeImmutable($guestyReservation['createdAt']))->format('Y-m-d h:m:s'),
                'object' => serialize($guestyReservation),
            ];

            if(!empty($guestyReservation['checkIn'])){
                $data['dateCheckin'] = (new \DateTimeImmutable($guestyReservation['checkIn']))->format('Y-m-d h:m:s');
            }
            if(!empty($guestyReservation['checkOut'])){
                $data['dateCheckout'] = (new \DateTimeImmutable($guestyReservation['checkOut']))->format('Y-m-d h:m:s');
            }
            if(!empty($guestyReservation['plannedArrival'])){
                $data['checking_time'] = $guestyReservation['plannedArrival'];
            }
            if(!empty($guestyReservation['plannedDeparture'])){
                $data['checkout_time'] = $guestyReservation['plannedDeparture'];
            }
            

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

            $reservation = $this->reservationService->findByIdRes($inputs['reservation']['_id']);
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
                Reservation::create($data);
            } else {
                if ($guest) {
                    if (strlen($reservation->email) == 0) {
                        $data['email'] = $guest['email'] ?? '';
                    }
                    if (strlen($reservation->phone) == 0) {
                        $data['phone'] = $guest['phone'] ?? '';
                    }
                    if (strlen($reservation->name) == 0) {
                        $data['name'] = $fullName;
                    }
                    if (strlen($reservation->prename) == 0) {
                        $data['prename'] = $guest['firstName'] ?? '';
                    }
                }
                $reservation->update($data);
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function guestWebhook(Request $request)
    {
        $inputs = $request->all();
        if (!isset($inputs['guest']) || empty($inputs['guest'])) {
            return response()->json(['errors' => ['guest' => [__('Input guest missing')]]], 422);
        }
        $guestyGuest = $inputs['guest'];
        Log::info('guesty webhook guest : ' . json_encode($inputs, JSON_UNESCAPED_SLASHES));
        $guest = $this->guestService->createOrUpdate($guestyGuest);
        return response()->json(['status' => $guest], 200);
    }

    public function listingWebhook(Request $request)
    {
        $inputs = $request->all();
        if (!isset($inputs['listing']) || empty($inputs['listing'])) {
            return response()->json(['errors' => ['listing' => [__('Input listing missing')]]], 422);
        }
        $guestyListing = $inputs['listing'];
        Log::info('guesty webhook listing : ' . json_encode($inputs, JSON_UNESCAPED_SLASHES));
        $this->listingService->createOrUpdate($guestyListing);

        $reservations = $this->reservationService->getByListingId($guestyListing['_id']);
        $beds = isset($guestyListing['beds']) ? intval($guestyListing['beds']) : 0;
        $address = isset($guestyListing['address']) ? $guestyListing['address']['full'] : '';
        foreach ($reservations as $reservation) {
            $reservation->update([
                'nombre_lits' => $beds,
                'name_adress' => $address,
            ]);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
