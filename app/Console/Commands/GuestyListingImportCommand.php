<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing;
use App\Services\GuestyService;
use App\Services\ListingService;

class GuestyListingImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesty:listing:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import guesty listings';

    private $guestyService;
    private $listingService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GuestyService $guestyService, ListingService $listingService)
    {
        parent::__construct();
        $this->guestyService = $guestyService;
        $this->listingService = $listingService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = $this->guestyService->getListings();
        foreach ($response['results'] as $guestyListing) {
            $checkingInstructions = isset($guestyListing['customFields']) && !empty($guestyListing['customFields']) ? $guestyListing['customFields'][0]['value'] : '';
            $data = [
                'listingId' => $guestyListing['_id'],
                'pictures' => serialize($guestyListing['pictures']),
                'checkingInstructions' => $checkingInstructions,
                'object' => serialize($guestyListing),
            ];
            $listing = Listing::where('listingId', $guestyListing['_id'])->first();
            if (!isset($listing)) {
                $listing = Listing::create($data);
            } else {
                $listing->update($data);
            }
            $this->info("Listing " . $guestyListing['_id'] . " imported");
        }
    }
}
