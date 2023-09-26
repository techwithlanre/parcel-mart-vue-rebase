<?php

namespace App\Tasks;

use App\Models\Shipment;
use App\Services\AramexServices;
use App\Services\DHLServices;
use App\Services\UpsServices;
use App\Traits\TrackingTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TrackingTask
{
    use TrackingTrait;

    public function trackDhl(): bool
    {
        $shipments = Shipment::whereNotIN('status', ['delivered', 'cancelled'])
            ->join('dhl_shipment_logs', 'shipments.id', '=', 'dhl_shipment_logs.shipment_id')->get();
        try {
            foreach ($shipments as $shipment) {
                $response = $this->sendDhlTrackingRequest($shipment->tracking_url);
                if ($response->status() != 200) {
                    continue;
                }
                $tracking_data = json_decode($response, true);
                $this->saveDhlTracking($tracking_data['shipments'], $shipment->shipment_id);
            }
            return true;
        } catch (\Throwable $e) {
            activity()
                ->performedOn(new Shipment())
                ->withProperties([
                    'method' => __CLASS__,
                    'action' => 'Cron DHL Track Shipment'
                ])
                ->log($e->getMessage());
            return false;
        }
    }

    private function sendDhlTrackingRequest($tracking_url): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        return Http::withBasicAuth("parcelmartsNG", "C^3zZ@4zJ!5iC#5m")
            ->withHeaders([
                'Message-Reference: ' . Str::uuid(),
                'Content-Type: application/json',
                'Accept: application/json',
            ])->get($tracking_url);
    }

    public function trackAramex(AramexServices $services)
    {

    }

    public function trackUps(UpsServices $services)
    {

    }
}