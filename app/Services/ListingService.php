<?php

namespace App\Services;

use App\Models\Listing;

class ListingService
{
    public function getAll()
    {
        return Listing::get();
    }

    public function createOrUpdate($guestyListing)
    {
        $checkingInstructions = '';
        if(isset($guestyListing['customFields']) && isset($guestyListing['customFields'][0])){
            $checkingInstructions = $guestyListing['customFields'][0]['fullText'] ?? '';
        }
        $data = [
            'listingId' => $guestyListing['_id'],
            'pictures' => serialize($guestyListing['pictures']),
            'checkingInstructions' => $checkingInstructions,
            'object' => serialize($guestyListing),
        ];
        $listing = Listing::where('listingId', $guestyListing['_id'])->first();
        if (!isset($listing)) {
            Listing::create($data);
        } else {
            $listing->update($data);
        }
        return Listing::where('listingId', $guestyListing['_id'])->first();
    }
}
