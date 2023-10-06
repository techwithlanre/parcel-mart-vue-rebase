<?php

use App\Events\ShipmentStatusUpdated;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Admin\UserController;
use \App\Http\Controllers\Admin\ShipmentController;

Route::get('', function () {
    return view('welcome');
});

Route::get('event', function () {
    ShipmentStatusUpdated::dispatch(\App\Models\Shipment::find(29));
});

Route::prefix('admin')->middleware('check.admin.user')->group(function () {
    Route::prefix('analytics')->group(function () {
        Route::get('', [\App\Http\Controllers\Admin\AnalyticController::class, 'index'])->name('admin.dashboard');
        Route::get('users', [\App\Http\Controllers\Admin\AnalyticController::class, 'users'])->name('admin.analytics.users');
        Route::get('shipments', [\App\Http\Controllers\Admin\AnalyticController::class, 'shipments'])->name('admin.analytics.shipments');
        Route::get('filter-shipments', [\App\Http\Controllers\Admin\AnalyticController::class, 'filterShipments'])->name('admin.analytics.shipments.filter');
    });

    Route::prefix('reports')->group(function () {
       Route::get('shipments', [\App\Http\Controllers\Admin\ReportsController::class, 'shipmentsReport'])->name('reports.shipment');
       Route::get('users', [\App\Http\Controllers\Admin\ReportsController::class, 'usersReport'])->name('reports.users');
       Route::get('users/{user_id}/shipments', [\App\Http\Controllers\Admin\ReportsController::class, 'userShipmentsReport'])->name('reports.users.shipments');
       Route::get('payments', [\App\Http\Controllers\Admin\ReportsController::class, 'paymentReport'])->name('reports.payments');
       Route::get('tax', [\App\Http\Controllers\Admin\ReportsController::class, 'taxReport'])->name('reports.tax');
    });

    Route::get('admin-filter-shipments', [\App\Http\Controllers\Admin\ShipmentController::class, 'filterShipment'])->name('admin.shipment.filter');

    Route::prefix('users')->group(function () {
        Route::get('', [UserController::class, 'index'])->name('users.index');
        Route::get('create', [UserController::class, 'create'])->name('users.create');
        Route::post('create', [UserController::class, 'store'])->name('users.store');
        Route::put('set-credit-limit/{user_id}', [UserController::class, 'setCreditLimit'])->name('users.set-credit-limit');
        Route::put('change-user-role/{user_id}', [UserController::class, 'changeUserRole'])->name('users.change-user-role');
    });

    //Route::resource('users', UserController::class);
    //Route::resource('shipments', ShipmentController::class);
    Route::prefix('shipments')->group(function () {
        Route::get('', [\App\Http\Controllers\Admin\ShipmentController::class, 'index'])->name('admin.shipment.index');
        Route::get('filter', [\App\Http\Controllers\Admin\ShipmentController::class, 'filterShipment'])->name('admin.shipment.filter');
        Route::post('calculate', [\App\Http\Controllers\Admin\ShipmentController::class, 'calculateShipment'])->name('admin.shipment.initialize');
        Route::put('update/{id}', [\App\Http\Controllers\Admin\ShipmentController::class, 'recalculateShipment'])->name('admin.shipment.recalculate');

        Route::get('pickup', [\App\Http\Controllers\Admin\ShipmentController::class, 'calculatePickup'])->name('admin.shipment.calculate.pickup');

        Route::get('origin/{id?}', [\App\Http\Controllers\Admin\ShipmentController::class, 'origin'])->name('admin.shipment.origin');
        Route::get('destination/{id}', [\App\Http\Controllers\Admin\ShipmentController::class, 'destination'])->name('admin.shipment.destination');
        Route::get('package-information/{id}', [\App\Http\Controllers\Admin\ShipmentController::class, 'packageInformation'])->name('admin.shipment.package-information');
        Route::post('store-origin/{id?}', [\App\Http\Controllers\Admin\ShipmentController::class, 'storeOrigin'])->name('admin.shipment.origin.store');
        Route::post('store-destination/{id}', [\App\Http\Controllers\Admin\ShipmentController::class, 'storeDestination'])->name('admin.shipment.destination.store');
        Route::post('store-package-information/{id}', [\App\Http\Controllers\Admin\ShipmentController::class, 'storePackageInformation'])->name('admin.shipment.package-information.store');
        Route::post('book', [\App\Http\Controllers\Admin\ShipmentController::class, 'bookShipment'])->name('admin.shipment.book');
        Route::get('checkout/{id}', [\App\Http\Controllers\Admin\ShipmentController::class, 'checkout'])->name('admin.shipment.checkout');
        Route::get('details/{id}', [\App\Http\Controllers\Admin\ShipmentController::class, 'show'])->name('admin.shipment.details');
        Route::post('track', [\App\Http\Controllers\Admin\ShipmentController::class, 'trackShipment'])->name('admin.shipment.track');
        Route::get('tracking-details/{shipment_id}', [\App\Http\Controllers\Admin\ShipmentController::class, 'trackingDetails'])->name('admin.shipment.track.details');
    });

    Route::resource('shipment-locations', \App\Http\Controllers\Admin\ShipmentLocationsController ::class);
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->middleware('role:admin');
    Route::post('roles/update-permissions/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])->middleware('role:admin')->name('roles.update-permissions');
    Route::prefix('settings')->group(function () {
        Route::get('rate', [\App\Http\Controllers\SettingController::class, 'rate'])->name('setting.rate');
        Route::get('rate/{id}', [\App\Http\Controllers\SettingController::class, 'editRate'])->name('setting.rate.edit');
        Route::put('rate/{id}', [\App\Http\Controllers\SettingController::class, 'updateRate'])->name('setting.rate.update');
    });

    Route::get('quotes', [\App\Http\Controllers\QuoteController::class, 'adminQuotes'])->name('admin.quotes');
    Route::put('set-quote-price/{id}', [\App\Http\Controllers\QuoteController::class, 'setPrice'])->name('admin.set-quote-price');
    Route::post('convert-quote-to-shipment/{id}', [\App\Http\Controllers\QuoteController::class, 'convertQuoteToShipment'])->name('admin.convert-quote-to-shipment');

    Route::prefix('feedback')->group(function () {
        Route::get('/tickets', [\App\Http\Controllers\CustomerFeedbackController::class, 'allTicket'])->name('feedback.tickets');
    });

    Route::prefix('locations')->group(function () {
        Route::get('', [\App\Http\Controllers\Admin\ShipmentLocationsController::class, 'index'])->name('countries');
        Route::get('countries', [\App\Http\Controllers\Admin\ShipmentLocationsController::class, 'index'])->name('countries');
        Route::get('states/{country_id}', [\App\Http\Controllers\Admin\ShipmentLocationsController::class, 'states'])->name('states');
        Route::get('cities/{state_id}', [\App\Http\Controllers\Admin\ShipmentLocationsController::class, 'cities'])->name('cities');
        Route::post('store-city', [\App\Http\Controllers\Admin\ShipmentLocationsController::class, 'storeCity'])->name('city.store');
    });
});
