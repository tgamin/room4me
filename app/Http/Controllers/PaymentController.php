<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Services\GuestyService;
use App\Services\ReservationService;
use App\Services\WordpressService;
use App\Services\OgoneService;
use App\Models\Cart;
use App\Models\CartItem;
use App\Mail\ReservationAdminMail;
use App\Notifications\ReservationCheckinInstructions;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $reservationService;
    private $guestyService;
    private $ogoneService;
    private $wordpressService;

    public function __construct(OgoneService $ogoneService)
    {
        $this->reservationService = new ReservationService();
        $this->guestyService = new GuestyService();
        $this->ogoneService = $ogoneService;
        $this->wordpressService = new WordpressService();
    }

    public function return(Request $request)
    {
        $inputs = $request->all();
        if(isset($inputs['hostedCheckoutId'])){
            $paid = false;
            for ($i = 0; $i < 5; $i++) {
                $ogonePayment = $this->ogoneService->getPayment($inputs['hostedCheckoutId'] . "_$i");
                if($ogonePayment && $ogonePayment->getStatus() == 'CAPTURED'){
                    $paid = true;
                } 
            }
            if ($paid) {
                return view('templates.payment.success', [
                    'display_confirmation' => true
                ]);
            }
        }
        return view('templates.payment.error');
    }

    public function success(Request $request)
    {
        $currentReservation = $this->reservationService->getCurrentReservation();
        if (!$currentReservation) {
            return redirect()->back()->withErrors(['msg' => __('No reservation found')]);
        }
        $listingOptions = $this->wordpressService->getListingOptions($currentReservation->listing->listingId);

        // Send checking instruction by email
        if ($currentReservation->instructionsCount === 0 && !$currentReservation->isComingFromBookingToday()) {
            try {
                $currentReservation->notify(new ReservationCheckinInstructions($currentReservation, $currentReservation->listing->checkingInstructions));
                $currentReservation->update(['instructionsCount' => $currentReservation->instructionsCount + 1]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
            $params = [
                'checkingInstructions' => $currentReservation->listing->checkingInstructions,
                'videos' => $listingOptions['videos'],
                'processBook' => $listingOptions['processBook'],
            ];
        }else{
            $params = [
                'display_confirmation' => true
            ];
        }

       
        return view('templates.payment.success', $params);
    }

    public function error()
    {
        return view('templates.payment.error');
    }

    public function cancel()
    {
        return view('templates.payment.cancel');
    }
    
    public function webhook(Request $request)
    {
        $inputs = $request->all();

        if(!isset($inputs['payment'])) {
            return response()->json(['status' => 'error', 'message' => __('Missing payment input')]);
        }
        $cart = Cart::where('paymentId', $inputs['payment']['id'])->first();
        if(!$cart) {
            return response()->json(['status' => 'error', 'message' => __('No payment found')]);
        }
        if ($inputs['payment']['status'] !== 'CAPTURED'){ 
            return response()->json(['status' => 'error', 'message' => __('Payment failed')]);
        }
        $reservation = $this->reservationService->findByIdRes($cart->reservationId);
        $body = $reservation->listing->checkingInstructions;
        if($cart->status != 'paid' && $body){
            $reservation->statut = 'paid';
            $reservation->servicesOrdered = true;
            $reservation->save();
            $cart->status = 'paid';
            $cart->save();

            $recipients = explode(',', env('MAIL_ADMIN_RECIPIENTS'));
            if (!empty($recipients)) {
                $admin = $recipients[0];
                unset($recipients[0]);
                Mail::to($admin)
                    ->cc($recipients)
                    ->send(new ReservationAdminMail($cart, $reservation));
            }

            // Add payment to Guesty
            $this->guestyService->addReservationPayment($reservation->idRes, $cart->total());
            foreach ($cart->items as $item) {
                if($item->title != 'Reservation') {
                    for ($i=0; $i < $item->quantity; $i++) { 
                        $this->guestyService->addReservationInvoiceItem($reservation->idRes, $item->title, $item->amount);
                    }
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
