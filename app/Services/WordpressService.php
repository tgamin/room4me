<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Class WordpressService
 * @package App\Service
 */
class WordpressService
{
    private $apiUrl;

    /**
     * WordpressService constructor.
     */
    public function __construct()
    {
        $this->apiUrl = env('WP_API_URL');
    }

    /**
     * Permet de récupérer les options WordPress
     */
    public function getOptions(): array
    {
        // $options = Http::get($this->apiUrl . 'acf/v3/options/options');
        $options = Http::withOptions(['verify' => false])->get($this->apiUrl . 'acf/v3/options/options');
        return isset($options['acf']) ? $options['acf'] : [];
    }

    /**
     * Permet de récupérer les catégories de services
     */
    public function getServicesCategories()
    {
        // return json_decode(Http::get($this->apiUrl . 'wp/v2/service_category')->getBody()->getContents(), true);
        return json_decode(Http::withOptions(['verify' => false])->get($this->apiUrl . 'wp/v2/service_category')->getBody()->getContents(), true);
    }

    /**
     * Permet de récupérer les options pour un bien
     */
    public function getListingOptions($listingId): array
    {
        $options = $this->getOptions();
        $result = [];
        if (isset($options['listings'])) {
            foreach ($options['listings'] as $listing) {
                if($listing['listingId'] == $listingId) {
                    $result = $listing;
                }
            }
        }
        return $result;
    }

    /**
     * Permet de récupérer les services de conciergerie pour un bien
     */
    public function getListingServices($listingId): array
    {
        $options = $this->getOptions();
        $services = [];
        if (isset($options['listings'])) {
            foreach ($options['listings'] as $listing) {
                if($listing['listingId'] == $listingId) {
                    if (isset($listing['services']) && !empty($listing['services'])) {
                        foreach ($listing['services'] as $listingService) {
                            if(isset($listingService['service']['categories']) && !empty($listingService['service']['categories'])){
                                foreach ($listingService['service']['categories'] as $category) {
                                    $services[$listingId][$category['term_id']][] = $listingService;
                                }
                            }
                        }
                    }
                }
            }
        }
        return isset($services[$listingId]) ? $services[$listingId] : [];
    }

    /**
     * Permet de récupérer les traduction depuis le BO WordPress
     */
    public function getTranslations(): array
    {
        $options = $this->getOptions();
        return isset($options['translations']) ? $options['translations'] : [];
    }
}

