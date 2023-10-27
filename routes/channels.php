<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('shipments.{shipmentId}', function ($user, $shipmentId) {
    return (int) $user->id === (int) \App\Models\Shipment::find($shipmentId)->user_id;
});