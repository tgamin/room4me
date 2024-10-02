<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\GuestyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('login');
})->name('home');
Route::get('/language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});
Route::get('/reservation/{confCode}/pay', [ReservationController::class, 'pay'])->name('reservation.pay');
Route::get('/reservation/find', [ReservationController::class, 'find'])->name('reservation.find');
Route::get('/reservation/{idRes}/edit', [ReservationController::class, 'edit'])->name('reservation.edit');
Route::get('/reservation/{idRes}/confirmation', [ReservationController::class, 'confirmation'])->name('reservation.confirmation');
Route::get('/reservation/{idRes}/newvalidation', [ReservationController::class, 'newvalidation'])->name('reservation.newvalidation');
Route::post('/reservation/{idRes}/update', [ReservationController::class, 'update'])->name('reservation.update');
Route::post('/reservation/{idRes}/update-document', [ReservationController::class, 'updateDocument'])->name('reservation.updateDocument');
Route::get('/cart-item/{cartItem}/remove', [CartItemController::class, 'remove'])->name( 'cart-item.remove');
Route::get('/cart/{cart}/add-cancellation-insurance', [CartController::class, 'addCancellationInsurance'])->name('cart.addCancellationInsurance');
Route::post('/cart/{cart}/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::get('/guesty/add-webhooks', [GuestyController::class, 'addWebhooks'])->name('guesty.webhooks.add');
Route::post('/guesty/webhook/reservation', [GuestyController::class, 'reservationWebhook'])->name('guesty.webhook.reservation');
Route::post('/guesty/webhook/listing', [GuestyController::class, 'listingWebhook'])->name('guesty.webhook.listing');
Route::post('/guesty/webhook/guest', [GuestyController::class, 'guestWebhook'])->name('guesty.webhook.guest');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
Route::get('/payment/return', [PaymentController::class, 'return'])->name('payment.return');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');
Route::resources([
    'cart' => CartController::class,
    'cart-item' => CartItemController::class,
]);

