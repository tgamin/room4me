<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Services\WordpressService;
use Illuminate\Support\Facades\URL;
use App\Services\ReservationService;
use App\Notifications\ReservationCheckinInstructions;

class ReservationController extends Controller
{
    private $reservationService;
    private $wordpressService;

    public function __construct()
    {
        $this->reservationService = new ReservationService();
        $this->wordpressService = new WordpressService();
    } 

    public function find(Request $request)
    {
        $inputs = $request->all();
        $request->validate([
            'confCode' => 'required',
            'dateCheckin' => 'required',
        ]);
        $reservation = $this->reservationService->find($inputs['confCode'], $inputs['dateCheckin']);
        if(!$reservation || ($reservation && !$reservation->idGuest)){
            return redirect()->back()->withErrors(['msg' => __('No reservation found')]);
        }
        session()->put('currentReservation', $reservation);
        $options = $this->wordpressService->getOptions();
        $reservations = $this->reservationService->getByGuestId($reservation->idGuest);
        //$invoiceLink = $this->guestyBookingService->getReservationInvoiceLink($reservations[0]['idRes']);
        return view('templates.reservation.index', compact('reservations', 'options'));
    }

    public function pay($confCode)
    {
        
        $services = [];
        $reservation = $this->reservationService->find($confCode);
        if (!$reservation) {
            return redirect()->route('home')->withErrors(['msg' => __('No reservation found')]);
        }
        
        session()->put('currentReservation', $reservation);
        $reservationObject = $reservation->getObject();
        $currentReservation = $this->reservationService->getCurrentReservation();
        if (!$currentReservation) {
            session()->put('currentReservation', $reservation);
            $currentReservation = $this->reservationService->getCurrentReservation();
        }
        $listingObject = $reservation->listing->getObject();
        // Save cart
        $cart = Cart::where('reservationId', $reservation->idRes)->first();
        if(!$cart){
            $cart = new Cart()  ;
            $cart->status = 'pending';
            $cart->reservationId = $reservation->idRes;
            $cart->save();
        }
        // Save cart reservation item
        $cartReservationItems = CartItem::where('cart_id', $cart->id)->where('title', 'Reservation')->get();

        //$cartReservationItems hadi katmse7 khas n3erfo 3lach
        foreach ($cartReservationItems as $cartItem) {
            $cartItem->delete();
        }

        //-------------------------
        if (!$reservation->isPaid()) {
            $balanceDue = $reservationObject['money']['balanceDue'] ?? 0;
            if ($balanceDue > 0) {
                $cartReservationItem = new CartItem();
                $cartReservationItem->cart_id = $cart->id;
                $cartReservationItem->post_id = 0;
                $cartReservationItem->title = 'Reservation';
                $cartReservationItem->amount = $balanceDue;
                $cartReservationItem->quantity = 1;
                $cartReservationItem->save();
            }
        }

        $cartItems = $cart->items ?? [];
        $services = $this->wordpressService->getListingServices($reservation->listing->listingId);
        $options = $this->wordpressService->getOptions();
        $servicesCategories = $this->wordpressService->getServicesCategories();
        $checkingTimes = $this->reservationService->getCheckingTimes();
        $checkoutTimes = $this->reservationService->getCheckoutTimes();
        $defaultCheckingTime = $currentReservation->checking_time ?? null;
        $defaultCheckoutTime = $currentReservation->checkout_time ?? null;

        // Cancellation insurance
        $cancellationInsurancePercentage = $options['cancellation_insurance'] ?? 1;
        $fareAccommodation = $reservationObject['money']['fareAccommodation'] ?? 0;
        $cancellationInsurancePrice = null;
        if($fareAccommodation > 0) {
            $cancellationInsurancePrice = ($fareAccommodation * $cancellationInsurancePercentage) / 100;
        }
        //for the test of api response
        // dd($reservation);


        return view('templates.reservation.pay', compact(
            'reservation',
            'currentReservation',
            'reservationObject',
            'listingObject',
            'services',
            'servicesCategories',
            'cart',
            'cartItems',
            'checkingTimes',
            'checkoutTimes',
            'defaultCheckingTime',
            'defaultCheckoutTime',
            'cancellationInsurancePrice',
        ));
    }

    public function edit($idRes)
    {
        $reservation = $this->reservationService->findByIdRes($idRes);
        $currentReservation = $this->reservationService->getCurrentReservation();
        if (!$reservation || ($reservation && !$reservation->idGuest)) {
            return redirect()->back()->withErrors(['msg' => __('No reservation found')]);
        }
        return view('templates.reservation.edit', compact('currentReservation'));
    }

    public function update($idRes, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'prename' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'identity_document' => 'mimes:pdf,jpg,png,gif,bmp',
        ]);
        $inputs = $request->all();
        $ajax = isset($inputs['reservationId']);
        if ($ajax) {
            unset($inputs['reservationId']);
        }
        $reservation = $this->reservationService->findByIdRes($idRes);
        if (!$reservation || ($reservation && !$reservation->idGuest)) {
            return redirect()->back()->withErrors(['msg' => __('No reservation found')]);
        }
        if (!$ajax && !$reservation->identity_document) {
            $request->validate(['identity_document' => 'required']);
        }
        // Save file
        $file = $request->file('identity_document');
        if($file){
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            if($filePath) {
                $inputs['identity_document'] = URL::to('/') . '/storage/' . $filePath;
            }
        }
        $this->reservationService->updateAllGuestReservations($reservation, $inputs);
        session()->put('currentReservation', $reservation);
        if ($ajax) {
            return response()->json('success');
        }
        return redirect()->back()->withSuccess(__('Successful update'));
    }

    public function updateDocument($idRes, Request $request)
    {
        $request->validate([
            'identity_document' => 'mimes:pdf,jpg,png,gif,bmp',
        ]);
        $reservation = $this->reservationService->findByIdRes($idRes);
        if (!$reservation || ($reservation && !$reservation->idGuest)) {
            return redirect()->back()->withErrors(['msg' => __('No reservation found')]);
        }
        // Save file
        $inputs = [];
        $file = $request->file('identity_document');
        if($file){
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            if($filePath) {
                $inputs['identity_document'] = URL::to('/') . '/storage/' . $filePath;
            }
        }
        $this->reservationService->updateAllGuestReservations($reservation, $inputs);
        return response()->json($inputs['identity_document']);
    }

    public function confirmation($confCode)
    {
        $reservation = $this->reservationService->find($confCode);
        $listingOptions = $this->wordpressService->getListingOptions($reservation->listing->listingId);
        $checkingInstructions = $reservation->listing->checkingInstructions;
        $checkingInstructions .= "<h2>" . __('Videos') . "</h2>";
        $checkingInstructions .= '<p><a href="' . route('reservation.confirmation', ['idRes' => $confCode]) . '">' . __('Videos') . '</a></p>';
        if(isset($listingOptions['processBook'])){
            $checkingInstructions .= "<h2>" . __('Process book') . "</h2>";
            $checkingInstructions .= $listingOptions['processBook'];
        }

        // Send checking instruction by email
        if(!$reservation->isComingFromBookingToday()){
            try {
                $reservation->notify(new ReservationCheckinInstructions($reservation, $checkingInstructions));
                $reservation->update(['instructionsCount' => $reservation->instructionsCount + 1]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
            $params = [
                'checkingInstructions' => $checkingInstructions,
                'videos' => $listingOptions['videos'] ?? '',
                'processBook' => $listingOptions['processBook'] ?? '',
            ];
        }else{
            $params = [
                'display_confirmation' => true
            ];
        }

        return view('templates.payment.success', $params);
    }



    public function newvalidation($confCode)
    {
        echo $confCode;
        $reservation = $this->reservationService->find($confCode);
        $reservation->update(['is_validated' => 1]);
        $listingOptions = $this->wordpressService->getListingOptions($reservation->listing->listingId);
        $checkingInstructions = $reservation->listing->checkingInstructions;
        $checkingInstructions .= "<h2>" . __('Videos') . "</h2>";
        $checkingInstructions .= '<p><a href="' . route('reservation.confirmation', ['idRes' => $confCode]) . '">' . __('Videos') . '</a></p>';
        if(isset($listingOptions['processBook'])){
            $checkingInstructions .= "<h2>" . __('Process book') . "</h2>";
            $checkingInstructions .= $listingOptions['processBook'];
        }

        // Send checking instruction by email
        if(!$reservation->isComingFromBookingToday()){
            try {
                $reservation->notify(new ReservationCheckinInstructions($reservation, $checkingInstructions));
                $reservation->update(['instructionsCount' => $reservation->instructionsCount + 1]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
            $params = [
                'checkingInstructions' => $checkingInstructions,
                'videos' => $listingOptions['videos'] ?? '',
                'processBook' => $listingOptions['processBook'] ?? '',
            ];
        }else{
            $params = [
                'display_confirmation' => true
            ];
        }

        return view('templates.payment.success', $params);
    }
}
