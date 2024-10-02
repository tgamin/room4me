<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Option;
use Illuminate\Support\Facades\Log;

/**
 * Class GuestyService
 * @package App\Services
 */
class GuestyService
{
    private $apiUrl;
    private $apiEndpointUrl;
    private $clientId;
    private $clientSecret;
    private $client;
    private $token;

    /**
     * GuestyService constructor.
     */
    public function __construct()
    {
        $this->apiEndpointUrl = env('GUESTY_API_URL');
        // Open api


        $this->apiUrl = env('GUESTY_OPEN_API_URL');
        $this->apiEndpointUrl = $this->apiUrl . 'v1/';
        $this->clientId = env('GUESTY_OPEN_API_CLIENT_ID');
        $this->clientSecret = env('GUESTY_OPEN_API_CLIENT_SECRET');

        $this->client = Http::withBasicAuth(env('GUESTY_API_KEY'), env('GUESTY_API_SECRET'))/* ->withoutVerifying() */;

        $this->setClient();
    }

    /**
     * Permet de récupérer le compte guesty
     */
    public function setClient()
    {
        $this->setToken();
        $this->client = Http::asForm()->withToken($this->token);
    }

    /**
     * Permet de récupérer le token
     */
    public function setToken()
    {
        $savedToken = Option::where('name', 'guesty_open_api_token')->first();
        $this->token = $savedToken->value ?? null;
    }

    /**
     * Permet de regénerer le token
     */
    public function renewToken()
    {
        return Http::asForm()->withHeaders([
            'Accept' => 'application/json',
        ])->post($this->apiUrl . "oauth2/token", [
            'grant_type' => 'client_credentials',
            'scope' => 'open-api',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ])->json();
    }

    /**
     * Permet de récupérer le compte guesty
     */
    public function getAccount()
    {
        return $this->client->get($this->apiEndpointUrl . "accounts/me")->json();
    }

    /**
     * Permet de récupérer une réservation
     */
    public function getReservation($id, $fields = null)
    {
        $params = [];
        if ($fields) {
            $params['fields'] = $fields;
        }
        return $this->client->get($this->apiEndpointUrl . "reservations/$id", $params)->json();
    }

    /**
     * Permet de récupérer des réservations
     */
    public function getReservations($fields = 'status canceledAt money confirmationCode', $statuses = null)
    {
        $i = 0;
        $results = [];
        $params = [
            'fields' => $fields,
            'limit' => 100, // max 100
            'sort' => '-lastUpdatedAt',
            'skip' => $i,
        ];
        if ($statuses) {
            $params['filters'] = [[
                'field' => "status",
                "operator" => "\$in",
                "value" => $statuses,
            ]];
        }
        $params['filters'] = [[
            'field' => "checkIn",
            "operator" => "\$gt",
            "value" => "2022-02-07",
        ]];
        while (count($results) < 9000) {
            $response = json_decode($this->client->get(
                $this->apiEndpointUrl . "reservations",
                $params
            ), true);
            $results = array_merge($results, $response['results']);
            $i++;
        }
        return $results;
    }

    /**
     * Permet de récupérer des réservations entre deux dates
     */
    public function getReservationsBeetween($start, $end = null)
    {
        $i = 0;
        $results = [];
        $nbItems = 100;

        while (count($results) < 1000) {
            $params = [
                'fields' => 'id createdAt status checkIn checkOut confirmationCode listing guest money listing preCancelationMoney integration numberOfGuests guestsCount nightsCount',
                'limit' => $nbItems, // max 100
                'sort' => 'createdAt',
                'skip' => $i * $nbItems,
                'filters' => [
                    [
                        'field' => "createdAt",
                        "operator" => "\$gt",
                        "value" => $start,
                    ],
                    [
                        'field' => "status",
                        "operator" => "\$nin",
                        "value" => ['inquiry'],
                    ]
                ]
            ];
            if ($end) {
                $params['filters'][] = [
                    'field' => "createdAt",
                    "operator" => "\$lt",
                    "value" => $end,
                ];
            }
            $response = $this->client->get($this->apiEndpointUrl . "reservations", $params)->json();
            if (empty($response['results'])) {
                return $results;
            }
            $results = array_merge($results, $response['results']);
            $i++;
        }
        return $results;
    }

    /**
     * Permet de trouver des réservations à partir de leur confCode
     */
    public function getReservationsByConfCodes($confCodes)
    {

        $response = $this->client->get(
            $this->apiEndpointUrl . "reservations",
            [
                'fields' => 'status canceledAt money confirmationCode',
                'limit' => 5, // max 100
                'filters' => [
                    [
                        'field' => "confirmationCode",
                        "operator" => "\$in",
                        "value" => $confCodes,
                    ],
                ]
            ]
        )->json();
        return $response['results'] ?? false;
    }

    /**
     * Permet de récupérer tous les webhooks du compte
     */
    public function getWebhooks()
    {
        return $this->client->get($this->apiEndpointUrl . "webhooks")->json();
    }

    /**
     * Permet d'ajouter un webhook
     */
    public function addWebhook($url, $events)
    {
        $account = $this->getAccount();
        return $this->client->post($this->apiEndpointUrl . "webhooks", [
            "url" => $url,
            "accountId" => $account['_id'],
            "events" => $events,
        ]);
    }

    /**
     * Permet de mettre à jour une réservation
     */

    //////////////////////////////////////////
    // public function updateReservation($id, $params)
    // {
    //     return $this->client->put($this->apiEndpointUrl . "reservations/$id", $params)->json();
    // }

    // public function updateReservation($id, $params)
    // {
    //     try {
    //         // Send the request to update the reservation
    //         $response = $this->client->put($this->apiEndpointUrl . "reservations/$id", $params);

    //         // Check if the response is successful
    //         if ($response->getStatusCode() !== 200) {
    //             $statusCode = $response->getStatusCode();
    //             $errorMessage = $response->getReasonPhrase();
    //             $errorDetails = $response->getBody()->getContents(); // Get detailed error response

    //             // Log the error
    //             Log::error("Failed to update reservation: $statusCode - $errorMessage", ['response' => $errorDetails]);

    //             // Return or throw an error
    //             throw new \Exception("Failed to update reservation: $statusCode - $errorMessage");
    //         }

    //         // Log success
    //         Log::info("Reservation $id updated successfully.", ['response' => $response->json()]);

    //         // Return the response
    //         return $response->json();
    //     } catch (\Exception $e) {
    //         // Log the exception
    //         Log::error("An error occurred while updating reservation: " . $e->getMessage());

    //         // You may choose to rethrow the exception or return a custom response
    //         throw $e;
    //     }
    // }

    public function updateReservation($id, $params)
    {
        try {
            Log::info("Updating reservation $id with params: " . json_encode($params));

            $response = $this->client->put($this->apiEndpointUrl . "reservations/$id", $params);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info("Reservation updated successfully: " . json_encode($responseData));
                return $responseData;
            } else {
                $statusCode = $response->status();
                // $errorMessage = $response->reasonPhrase();
                $errorDetails = $response->json();

                Log::error("Guesty API error: $statusCode ", ['response' => $errorDetails]);

                throw new \Exception("Failed to update reservation: $statusCode");
            }
        } catch (\Exception $e) {
            Log::error("Guesty API error: " . $e->getMessage());
            throw $e;
        }
    }

    // code de put les donnees a guesty 
    // public function updateReservation($id, $params)
    // {
    //     $endpoint = $this->apiEndpointUrl . "reservations/$id";

    //     $response = $this->client->put($endpoint, $params)/* ->withoutVerifying() */;

    //     if ($response->successful()) {
    //         return $response->json();
    //     } else {
    //         Log::error("Failed to update reservation in Guesty", ['response' => $response->body()]);
    //         return ['error' => 'Failed to update reservation in Guesty', 'status' => $response->status()];
    //     }
    // }

    /**
     * Permet de mettre à jour un webhook
     */
    public function updateWebhook($id, $url, $events)
    {
        return $this->client->put($this->apiEndpointUrl . "webhooks/$id", [
            "url" => $url,
            "events" => $events,
        ])->json();
    }

    /**
     * Permet de supprimer un webhook
     */
    public function deleteWebhook($id)
    {
        return $this->client->delete($this->apiEndpointUrl . "webhooks/$id")->json();
    }

    /**
     * Permet d'ajouter un paiement à une réservation
     */
    public function addReservationPayment($id, $amount)
    {
        return $this->client->post($this->apiEndpointUrl . "reservations/$id/payments", [
            "paymentMethod" => [
                "method" => 'DEBIT'
            ],
            "amount" => $amount
        ]);
    }

    /**
     * Permet d'ajouter une ligne de facturation à une réservation
     */
    public function addReservationInvoiceItem($id, $title, $amount, $description = '')
    {
        return $this->client->post($this->apiEndpointUrl . "reservations/$id/invoiceItems", [
            "paymentMethod" => [
                "method" => 'DEBIT'
            ],
            "title" => $title,
            "amount" => $amount,
            "description" => $description,
        ]);
    }

    /**
     * Permet de récupérer les listings
     */
    public function getListings()
    {
        return $this->client->get($this->apiEndpointUrl . "listings", [
            'limit' => 100,
        ])->json();
    }

    /**
     * Permet de récupérer un listing
     */
    public function getListing($id)
    {
        return $this->client->get($this->apiEndpointUrl . "listings/$id")->json();
    }

    /**
     * Permet de récupérer un listing
     */
    public function getGuests()
    {
        $response = $this->client->get($this->apiEndpointUrl . "guests-crud", [
            'columns' => 'id fullName firstName lastName guestEmail guestPhone address hometown',
            'limit' => 25000,
        ])->json();
        return $response['results'];
        /*$i = 0;
        $results = [];
        while(count($results) < 23000) {
            $response = $this->client->get($this->apiEndpointUrl . "guests-crud", [
                'columns' => 'id fullName firstName lastName guestEmail guestPhone address hometown',
                'skip' => $i,
            ])->json();
            $results = array_merge($results, $response['results']);
            $i ++;
        }
        return $results;*/
    }

    /**
     * Permet de récupérer un listing
     */
    public function getGuest($id)
    {
        return $this->client->get($this->apiEndpointUrl . "guests-crud/$id", [
            'fields' => 'id fullName firstName lastName email phone address hometown',
        ])->json();
    }
}
