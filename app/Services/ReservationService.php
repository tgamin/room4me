<?php

namespace App\Services;

use App\Models\Reservation;

class ReservationService
{
    public function getAll()
    {
        return Reservation::get();
    }

    public function getByListingId($listingId)
    {
        return Reservation::select('*')
            ->join('listings', 'reservations.listing_id', '=', 'listings.id')
            ->where('listings.listingId', $listingId)->get();
    }

    public function getByGuestId($idGuest)
    {
        return Reservation::where('idGuest', $idGuest)->get();
    }

    public function getFromToday()
    {
        $query = Reservation::where('dateCheckin', '>=', date('Y-m-d'));
        return $query->get();
    }

    public function getPaid()
    {
        $query = Reservation::where(function ($query) {
            $query->where('statut', 'paid')->orWhere('debit_card_statut', 'paid');
        });
        $query = $query->whereBetween('dateCheckin', [date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime('+2 days'))]);
        //$query = $query->where('smsCount', '<', 3);
        $query = $query->where('servicesOrdered', false);
        $query = $query->whereNull('checking_time');
        $query = $query->whereNull('checkout_time');
        $query = $query->whereNull('identity_document');
        return $query->get();
    }

    public function getByStatus($status, $maxSms = 1)
    {
        $query = Reservation::where('statut', $status)->where('servicesOrdered', false);
        if($maxSms > 0) {
            $query = $query->where('smsCount', '<', $maxSms);
        }
        return $query->get();
    }

    public function getCurrentReservation()
    {
        $currentReservation = session()->get('currentReservation');
        if($currentReservation && $currentReservation->email) {
            $exludedDomains = [
                'booking',
                'airbnb',
                'expediapartnercentral',
                'homeaway',
            ];
            $found = 0;
            $emailTab = explode('@', $currentReservation->email);
            if(isset($emailTab[1])){
                foreach ($exludedDomains as $exludedDomain) {
                    if (strrpos($emailTab[1], $exludedDomain)) {
                        $found++;
                    }
                }
            }
            if ($found > 0) {
                $currentReservation->email = null;
            }
        }
        return $currentReservation;
    }

    public function updateAllGuestReservations($res, $inputs)
    {
        if(isset($inputs['_token'])) unset($inputs['_token']);
        if(isset($inputs['idRes'])) unset($inputs['idRes']);
        $reservations = $this->getByGuestId($res->idGuest);
        foreach ($reservations as $reservation) {
            $reservation->update($inputs);
        }
        return true;        
    }

    public function find($confCode, $dateCheckin = null)
    {
        $query = Reservation::where('confCode', $confCode);
        if($dateCheckin) {
            $query = $query->where('dateCheckin', $dateCheckin);
        }
        $result = $query->first();
        if($result === null){
            //$query = Reservation::where('name', $confCode);
            $query = Reservation::where('name', 'like', '%' . $confCode . '%');
            if ($dateCheckin) {
                $query = $query->where('dateCheckin', $dateCheckin);
            }
            return $query->first();
        }
        return $result;
    }

    public function findByIdRes($idRes)
    {
        return Reservation::where('idRes', $idRes)->first();
    }

    public function getAllWithoutListingId()
    {
        return Reservation::whereNull('idListing')->get();
    }

    public function getConfCodesWithoutListingId($offset)
    {
        return Reservation::whereNull('idListing')->offset($offset)->limit(10)->pluck('confCode')->toArray();
    }

    public function getCheckingTimes()
    {
        $times = $this->getTimeInterval('12:00','23:30');
        $times[] = '00:00';
        return $times;
    }

    public function getCheckoutTimes()
    {
        return $this->getTimeInterval('00:00','14:00');
    }

    public function getTimeInterval($starttime, $endtime, $duration = '30')
    {
        $array_of_time = array();
        $start_time    = strtotime($starttime);
        $end_time      = strtotime($endtime);
        
        $add_mins  = $duration * 60;

        while ($start_time <= $end_time)
        {
            $array_of_time[] = date("H:i", $start_time);
            $start_time += $add_mins; // to check endtie=me
        }

        return $array_of_time;
    }
}
