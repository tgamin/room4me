<?php

namespace App\Services;

use App\Models\Guest;

class GuestService
{
    public function getAll()
    {
        return Guest::get();
    }

    public function createOrUpdate($guestyGuest)
    {
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
            Guest::create($data);
        } else {
            $guest->update($data);
        }
        return Guest::where('guestId', $guestyGuest['_id'])->first();
    }
}
