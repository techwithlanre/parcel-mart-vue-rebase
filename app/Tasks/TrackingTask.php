<?php

namespace App\Tasks;

use App\Models\Shipment;
use App\Services\UpsServices;
use App\Traits\TrackingTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Octw\Aramex\Aramex;

class TrackingTask
{
    use TrackingTrait;

    private string $username;
    private string $password;
    private string $baseUrl;

    public function __construct()
    {
        $env = config('dhl.ENV');
        $this->baseUrl = config('dhl.'.$env.'.baseUrl');
        $this->username = config('dhl.'.$env.'.username');
        $this->password = config('dhl.'.$env.'.password');
    }

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
        return Http::withBasicAuth($this->username, $this->password)
            ->withHeaders([
                'Message-Reference: ' . Str::uuid(),
                'Content-Type: application/json',
                'Accept: application/json',
            ])->get($tracking_url);
    }

    public function trackAramex(): void
    {
        $shipments = Shipment::whereNotIN('status', ['delivered', 'cancelled'])
            ->join('aramex_shipment_logs', 'shipments.id', '=', 'aramex_shipment_logs.shipment_id')->get();

        try {
            foreach ($shipments as $shipment) {
                $response = Aramex::trackShipments([$shipment->number]);
                if ((isset($response->HasErrors) && $response->HasErrors) || (isset($response->error) && $response->error)) {
                    activity()
                        ->performedOn(new Shipment())
                        ->withProperties([
                            'method' => __FUNCTION__,
                            'action' => 'Cron: Aramex Track Shipment'
                        ])
                        ->log($response->errors->Notification->Message);
                    continue;
                }
                $this->saveAramexTracking($response, $shipment->id);
            }
        } catch (\Throwable  $e) {
            activity()
                ->performedOn(new Shipment())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'Cron: Aramex Track Shipment'
                ])
                ->log($e->getMessage());
        }
    }

    public function trackUps(UpsServices $services)
    {

    }
}