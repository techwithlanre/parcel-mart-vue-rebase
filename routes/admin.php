<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Admin\UserController;
use \App\Http\Controllers\Admin\ShipmentController;

Route::prefix('admin')->middleware('check.admin.user')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    Route::prefix('reports')->group(function () {
       Route::get('shipments', [\App\Http\Controllers\Admin\ReportsController::class, 'shipmentsReport'])->name('reports.shipment');
       Route::get('users', [\App\Http\Controllers\Admin\ReportsController::class, 'usersReport'])->name('reports.users');
       Route::get('payments', [\App\Http\Controllers\Admin\ReportsController::class, 'paymentReport'])->name('reports.payments');
       Route::get('tax', [\App\Http\Controllers\Admin\ReportsController::class, 'taxReport'])->name('reports.tax');
    });
    Route::prefix('feedback')->group(function () {
    Route::get('/tickets', [\App\Http\Controllers\CustomerFeedbackController::class, 'allTicket'])->name('feedback.tickets');
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
    Route::resource('shipments', ShipmentController::class);
    Route::resource('shipment-locations', \App\Http\Controllers\Admin\ShipmentLocationsController::class);
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->middleware('role:admin');
    Route::post('roles/update-permissions/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])->middleware('role:admin')->name('roles.update-permissions');
    Route::prefix('settings')->group(function () {
        Route::get('rate', [\App\Http\Controllers\SettingController::class, 'rate'])->name('setting.rate');
        Route::get('rate/{id}', [\App\Http\Controllers\SettingController::class, 'editRate'])->name('setting.rate.edit');
        Route::put('rate/{id}', [\App\Http\Controllers\SettingController::class, 'updateRate'])->name('setting.rate.update');
    });

});
