<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Admin\UserController;
use \App\Http\Controllers\Admin\ShipmentController;

Route::prefix('admin')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('shipments', ShipmentController::class);
    Route::prefix('settings')->group(function () {
        Route::get('rate', [\App\Http\Controllers\SettingController::class, 'rate'])->name('setting.rate');
        Route::get('rate/{id}', [\App\Http\Controllers\SettingController::class, 'editRate'])->name('setting.rate.edit');
        Route::put('rate/{id}', [\App\Http\Controllers\SettingController::class, 'updateRate'])->name('setting.rate.update');
    });
});
