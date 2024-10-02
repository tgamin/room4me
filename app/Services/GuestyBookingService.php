<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Option;

/**
 * Class GuestyBookingService
 * @package App\Services
 */
class GuestyBookingService
{
    private $apiUrl;
    private $apiId;
    private $apiSecret;

    /**
     * GuestyBookingService constructor.
     */
    public function __construct()
    {
        $this->apiUrl = env('GUESTY_BOOKING_API_URL');
        $this->apiId = env('GUESTY_BOOKING_API_ID');
        $this->apiSecret = env('GUESTY_BOOKING_API_SECRET');
    }

    public function getToken() {
        $savedToken = Option::where('name', 'guesty_booking_token')->first();
        if($savedToken){
            $expiresIn = Option::where('name', 'guesty_booking_token_expires_in')->first();
            $createdAt = Option::where('name', 'guesty_booking_token_created_at')->first();
            $tokenHasExpired = time() > strtotime($createdAt->value) + intval($expiresIn->value);
            if(!$tokenHasExpired){
                return $savedToken->value;
            }else{
                $savedToken->delete();
                $expiresIn->delete();
                $createdAt->delete();
            }
        }
        $res = $this->renewToken();

        if (isset($res['access_token'])) {
            Option::create([
                'name' => 'guesty_booking_token',
                'value' => $res['access_token'],
            ]);
            Option::create([
                'name' => 'guesty_booking_token_expires_in',
                'value' => $res['expires_in'],
            ]);
            Option::create([
                'name' => 'guesty_booking_token_created_at',
                'value' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function renewToken()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_USERPWD, 'ACCOUNTID' . ':' . 'PASSWORD');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            "client_id" => $this->apiId,
            "client_secret" => $this->apiSecret,
            "grant_type" => 'client_credentials',
            "scope" => 'booking_engine:api',
        ]));
        $resultRes = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($resultRes, true);

        return $res;
    }

    public function post($endpoint, $post = [])
    {
        //header('Content-Type: application/json'); // Specify the type of data
        $ch = curl_init($this->apiUrl . $endpoint); // Initialise cURL
        $post = json_encode($post); // Encode the data array into a JSON string
        $authorization = "Authorization: Bearer " . $this->getToken(); // Prepare the authorisation token
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json; charset=utf-8', $authorization)); // Inject the token into the header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
        $result = curl_exec($ch); // Execute the cURL statement
        curl_close($ch); // Close the cURL connection
        return json_decode($result); // Return the received data
    }

    /**
     * Permet de récupérer une réservation
     */
    public function getReservationInvoiceLink($reservationId)
    {

        $response = Http::withHeaders([
            'Accept' => 'application/json; charset=utf-8',
            'authorization' => 'Bearer ' . $this->getToken(),
        ])->get("https://app.guesty.com/api/share-invoice/private/?reservationId=$reservationId");

        dd($response->getBody()->getContents());
    }
}