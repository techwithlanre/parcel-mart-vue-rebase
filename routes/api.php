<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\Auth\UserAuthController;
use App\Http\Controllers\Mobile\General\GeneralController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['middleware'], function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/register', [UserAuthController::class, 'register'])->name('auth.user.register');
        Route::post('/login', [UserAuthController::class, 'login']);
        Route::get('/email/confirm/{token}', [UserAuthController::class, 'registerConfirm'])->name('email_confirmation');

        // Password reset routes
        Route::post('password/email',  [UserAuthController::class, 'forget_password']);
        Route::post('password/code/check', [MobileController::class, 'code_check']);
        Route::post('password/reset', [MobileController::class, 'reset_password']);

        Route::post('/deposit', [MobileController::class, 'deposit']);
    });

    Route::get('/country', [UserAuthController::class, 'country']);
    Route::get('/states/{id}', [UserAuthController::class, 'states']);
    Route::get('/cities/{id}', [UserAuthController::class, 'city']);
    // put all api protected routes here
    Route::middleware('auth:api')->group(function () {
        Route::get('/profile', [GeneralController::class, 'userprofile']);
        Route::post('/update/profile', [MobileController::class, 'update_profile']);
        Route::post('/update/password', [MobileController::class, 'update_password']);
        Route::post('/upload/profile-picture', [MobileController::class, 'upload_profile_picture']);
    });

    Route::get('/deposit/gateway/callback', [MobileController::class, 'handleDepositGatewayCallback'])->name('handleDepositGatewayCallback');
});
