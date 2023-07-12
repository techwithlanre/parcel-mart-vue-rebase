<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Admin\UserController;
use \App\Http\Controllers\Admin\ShipmentController;

Route::prefix('admin')->middleware('check.admin.user')->group(function () {
    Route::resource('users', UserController::class);
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
