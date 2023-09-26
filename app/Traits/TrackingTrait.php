<?php

namespace App\Traits;

use App\Models\TrackingLog;

trait TrackingTrait
{
    public static function saveDhlTracking($shipments, $shipment_id)
    {
        $tracking_log = false;
        foreach ($shipments as $shipment) {
            if (isset($shipment['events']) && count($shipment['events']) > 0) {
                foreach ($shipment['events'] as $event) {
                    $check = TrackingLog::where([
                        'shipment_id' => $shipment_id,
                        'update_code' => $event['typeCode'],
                        'provider' => 'dhl',
                    ])->first();
                    if (!$check) {
                        $tracking_log = TrackingLog::create([
                            'shipment_id' => $shipment->id,
                            'update_code' => $event['typeCode'],
                            'waybill_number' => $shipment['pieces'][0]['trackingNumber'],
                            'update_description' => $event['description'],
                            'update_datetime' => $event['date'] . ' ' . $event['time'],
                            'update_location' => $event['serviceArea'][0]['description'],
                            'comment' => $shipment['description'],
                            'gross_weight' => $shipment['totalWeight'],
                            'chargeable_weight' => $shipment['totalWeight'],
                            'weight_unit' => 'metric',
                            'provider' => 'dhl',
                        ]);
                    }
                }
            }
        }
        return $tracking_log;
    }
}