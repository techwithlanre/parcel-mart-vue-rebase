<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\RegisteredBusinessController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Home/Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'countries' => \App\Models\Country::all()
    ]);
})->name('home');

Route::get('coming-soon', function () {
    return Inertia::render('ComingSoon');
})->name('coming.soon');

Route::get('services', [\App\Http\Controllers\ServicesController::class, 'index'])->name('services');
Route::get('contact', [\App\Http\Controllers\ContactController::class, 'index'])->name('contact');
Route::get('company', [\App\Http\Controllers\AboutController::class, 'index'])->name('about');
Route::get('tracking', function () {
    return Inertia::render('Tracking/Tracking');
})->name('tracking');

Route::post('contact', [\App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');

Route::post('/send-quote-form', function(\Illuminate\Http\Request $request) {
    Mail::to('quotes@parcelsmartsolution.com')->send(new \App\Mail\QuoteForm($request));
    return redirect(\route('dashboard'))->with('message', 'Your request has been sent');
})->name('send.quote.form');


Route::get('states/{country_id}', function () {
    return response()->json([
        'states'=>\App\Models\State::where('country_id', request()->country_id)->get()
    ]);
});



Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register.index');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::post('business-register', [RegisteredBusinessController::class, 'store'])->name('register.business.index');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');


});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    /*Address Book*/
    Route::resource('address-book',\App\Http\Controllers\AddressBookController::class);
    //Route::resource('shipments', \App\Http\Controllers\ShipmentController::class);

    Route::prefix('shipments')->group(function () {
        Route::get('', [\App\Http\Controllers\ShipmentController::class, 'index'])->name('shipment.index');
        Route::get('filter', [\App\Http\Controllers\ShipmentController::class, 'filterShipment'])->name('shipment.filter');
        Route::get('start', [\App\Http\Controllers\ShipmentController::class, 'create'])->name('shipment.start');
        Route::post('calculate', [\App\Http\Controllers\ShipmentController::class, 'calculateShipment'])->name('shipment.initialize');
        Route::put('update/{id}', [\App\Http\Controllers\ShipmentController::class, 'recalculateShipment'])->name('shipment.recalculate');
        Route::get('checkout/{id}', [\App\Http\Controllers\ShipmentController::class, 'checkout'])->name('shipment.checkout');
        Route::post('book', [\App\Http\Controllers\ShipmentController::class, 'bookShipment'])->name('shipment.book');
        Route::get('details/{id}', [\App\Http\Controllers\ShipmentController::class, 'show'])->name('shipment.details');
        Route::post('track', [\App\Http\Controllers\ShipmentController::class, 'trackShipment'])->name('shipment.track');
        Route::get('tracking-details/{shipment_id}', [\App\Http\Controllers\ShipmentController::class, 'trackingDetails'])->name('shipment.track.details');
        Route::get('pickup', [\App\Http\Controllers\ShipmentController::class, 'calculatePickup'])->name('shipment.calculate.pickup');
    });

    /*Wallet*/
    Route::prefix('wallet')->group(function () {
        Route::get('', [\App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
        Route::get('filter', [\App\Http\Controllers\WalletController::class, 'filterTransactions'])->name('wallet.filter');
        Route::post('initialize', [App\Http\Controllers\WalletController::class, 'initialize'])->name('wallet.initialize');
        Route::get('callback', [App\Http\Controllers\WalletController::class, 'paystackWebhook'])->name('wallet.paystack.webhook');
    });

    /*Settings*/
    Route::prefix('settings')->group(function () {
       Route::get('/', [\App\Http\Controllers\SettingController::class, 'index'])->name('setting.index');
       Route::prefix('shipping')->group(function () {
           Route::get('description', [\App\Http\Controllers\ShippingSettingController::class, 'description'])->name('shipping.setting.description');
           Route::post('description', [\App\Http\Controllers\ShippingSettingController::class, 'createDescription'])->name('shipping.setting.description.post');
           Route::get('measurement', [\App\Http\Controllers\ShippingSettingController::class, 'measurement'])->name('shipping.setting.measurement');
       });
    });

    Route::get('faq', [\App\Http\Controllers\FaqController::class, 'index'])->name('faq.index');


    Route::prefix('billing')->group(function (){
        Route::get('', [\App\Http\Controllers\BillingController::class, 'cards'])->name('billing.cards.get');
        Route::get('cards', [\App\Http\Controllers\BillingController::class, 'cards'])->name('billing.cards.get');
        Route::post('cards', [\App\Http\Controllers\BillingController::class, 'createCard'])->name('billing.cards.post');
    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
Route::put('password', [PasswordController::class, 'update'])->name('password.update');
Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

//////////////


//TODO
Route::get('workbench/{id}', function (\Illuminate\Http\Request $request) {
   $cc = \App\Models\AllowedShipmentCountry::where('country_id', $request->id)->first();
   $origin = $cc->country_id;
   $destinations = explode(',', $cc->allowed_destinations);
   $data = [];
   foreach ($destinations as $d) {
       $data[] = [
           'id' => $d,
           'name' => getCountry('id', $d)->name
       ];
   }
   echo '<pre>';
   print_r($data);
   exit;
});