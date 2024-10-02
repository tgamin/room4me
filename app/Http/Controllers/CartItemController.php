<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Services\ReservationService;

class CartItemController extends Controller
{
    private $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Store a new cart item.
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
        $cart = Cart::where('reservationId', $inputs['reservationId'])->first();
        if(!$cart){
            return redirect()->back()->withErrors(['msg' => __('No reservation found')]);
        }
        $cartItem = CartItem::where('cart_id', $cart->id)->where('post_id', $inputs['post_id'])->first() ?? new CartItem();
        $cartItem->cart_id = $cart->id;
        $cartItem->post_id = $inputs['post_id'];
        $cartItem->title = $inputs['title'];
        $cartItem->amount = $inputs['amount'];
        $cartItem->quantity = $cartItem->quantity + intval($inputs['quantity']);
        $cartItem->save();

        $reservationInputs = [
            'checking_time' => $inputs['checking_time'],
            'checkout_time' => $inputs['checkout_time'],
        ];
        $reservation = $this->reservationService->findByIdRes($inputs['reservationId']);
        $reservation->update($reservationInputs);

        return redirect()->back()->withSuccess(__('Service added to cart'));
    }

    public function remove(CartItem $cartItem)
    {
        $reservation = $this->reservationService->findByIdRes($cartItem->cart->reservationId);
        if($cartItem->post_id == '505'){
            $reservation->update(['checking_time' => null]);
        }
        if($cartItem->post_id == '508'){
            $reservation->update(['checkout_time' => null]);
        }
        if($cartItem->title == __('Cancellation insurance')){
            $reservation->update(['insurance' => null]);
        }
        $cartItem->delete();
        return redirect()->back()->withSuccess(__('Service deleted from cart'));
    }
}
