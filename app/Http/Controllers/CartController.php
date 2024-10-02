<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\GuestyService;
use App\Services\OgoneService;
use App\Services\ReservationService;
use App\Services\WordpressService;
use Illuminate\Support\Facades\URL;

class CartController extends Controller
{
    private $guestyService;
    private $ogoneService;
    private $reservationService;
    private $wordpressService;

    public function __construct(GuestyService $guestyService, OgoneService $ogoneService, ReservationService $reservationService, WordpressService $wordpressService)
    {
        $this->ogoneService = $ogoneService;
        $this->reservationService = $reservationService;
        $this->wordpressService = $wordpressService;
        $this->guestyService = $guestyService;
    }

    public function checkout(Cart $cart, Request $request)
    {
        $inputs = $request->all();
        $idRes = $request->input('idRes');
        // dd($idRes);
        $reservation = $this->reservationService->findByIdRes($inputs['idRes']);
        if (!$reservation || ($reservation && !$reservation->idGuest)) {
            return redirect()->back()->withErrors(['msg' => __('No reservation found')]);
        }
        $nbLits = $reservation->nombre_lits ?? 0;
        $rules = [
            'name' => 'required',
            'prename' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'checking_time' => 'required',
            'checkout_time' => 'required',
            //'beds_to_prepare' => "required|numeric|min:0|max:$nbLits",
            'double_beds' => "required|numeric|min:0|max:5",
            'single_beds' => "required|numeric|min:0|max:5",
            'identity_document' => 'mimes:pdf,jpg,png,gif,bmp',
        ];
        if (empty($reservation->identity_document)) {
            $rules['identity_document'] = "required|" . $rules['identity_document'];
        }

        $request->validate($rules);
        unset($inputs['reservationId']);

        // Save file
        $file = $request->file('identity_document');
        if ($file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            if ($filePath) {
                $inputs['identity_document'] = URL::to('/') . '/storage/' . $filePath;
            }
        }

        $allReservationsInputs = [
            'name' => $inputs['name'],
            'prename' => $inputs['prename'],
            'email' => $inputs['email'],
            'phone' => $inputs['phone'],
        ];
        $this->reservationService->updateAllGuestReservations($reservation, $allReservationsInputs);

        if (isset($inputs['identity_document'])) {
            $allReservationsInputs['identity_document'] = $inputs['identity_document'];
        }

        $reservation->update([
            'checking_time' => $inputs['checking_time'],
            'checkout_time' => $inputs['checkout_time'],
            //'beds_to_prepare' => $inputs['beds_to_prepare'],
            'double_beds' => $inputs['double_beds'],
            'single_beds' => $inputs['single_beds'],
        ]);
        //for guesty 25/09
        // $reservationParams = [
        //     'checkInDate' => $inputs['checking_time'],
        //     'checkOutDate' => $inputs['checkout_time'],
        //     'doubleBeds' => $inputs['double_beds'],
        //     'singleBeds' => $inputs['single_beds'],
        // ];

        // dd($reservationParams);
        // $this->guestyService->updateReservation($idRes, $reservationParams);

        $cart = Cart::find($cart->id);

        // Update reservation checkin and checkout times
        // hada howa dyale time li bghaw yb9a ytbadal fih
        $reservationParams = [];
        if($inputs['checking_time']){
            $reservationParams['plannedArrival'] = $inputs['checking_time'];
        }
        if($inputs['checkout_time']){
            $reservationParams['plannedDeparture'] = $inputs['checkout_time'];
        }
        // dd($reservationParams);
        $this->guestyService->updateReservation($idRes, $reservationParams);

        // Send email if coming from airbnb
        if ($cart->total() === 0) {
            return redirect()->route('reservation.confirmation', $reservation->confCode)->withSuccess(__('Successful update'));
        } else {
            $ogoneResult = $this->ogoneService->getHostedCheckoutPage($cart->total());
            $cart->status = 'pending';

            $cart->paymentId = $ogoneResult->getHostedCheckoutId() . '_0';
            $cart->macAddress = $ogoneResult->getReturnMac();
            $cart->save();

            return redirect()->away($ogoneResult->getRedirectUrl());
        }
    }

    public function addCancellationInsurance(Cart $cart, Request $request)
    {
        $options = $this->wordpressService->getOptions();
        $reservation = $this->reservationService->findByIdRes($cart->reservationId);
        $reservationObject = $reservation->getObject();
        $cancellationInsurancePercentage = $options['cancellation_insurance'] ?? 1;
        $fareAccommodation = $reservationObject['money']['fareAccommodation'] ?? 0;
        if ($fareAccommodation > 0) {
            $cancellationInsurancePrice = ($fareAccommodation * $cancellationInsurancePercentage) / 100;

            $cartItem = new CartItem();
            $cartItem->cart_id = $cart->id;
            $cartItem->title = __('Cancellation insurance');
            $cartItem->amount = $cancellationInsurancePrice;
            $cartItem->quantity = 1;
            $cartItem->save();

            $reservation->update(['insurance' => $cancellationInsurancePrice]);
        }

        return redirect()->back()->withSuccess(__('Service added to cart'));
    }

    public function callback(Cart $cart, $paymentId)
    {
        //set fields that needs to be validated in ipn response, query own db first
        $this->payment->addFieldOrder("currency", "EUR");
        $this->payment->addFieldOrder("total", $cart->total());
        //validate the callback
        $obj_result = $this->payment->validateIpn($_POST);
    }
}
