<?php

namespace App\Services;

use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Http\Requests\TrackShipmentRequest;
use App\Models\InsuranceOption;
use App\Models\ItemCategory;
use App\Models\Shipment;
use App\Models\ShipmentAddress;
use App\Models\ShipmentItem;
use App\Models\ShippingRateLog;
use App\Models\UpsShipmentLog;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Ups\Entity\Address as AddressAlias;
use Ups\SimpleAddressValidation;

class UpsServices
{
    public Shipment $shipment;
    public array $originAddressPayload = [];
    public array $destinationAddressPayload = [];
    public array $shipmentDetailsPayload = [];
    public string $originCountryCode = '';
    public string $destinationCountryCode = '';
    public string $shippingCurrency = '';
    protected string $baseUrl = '';
    public string $clientId = '';
    public string $clientSecret = '';
    protected string $credentials = '';
    public string $accessToken = '';
    protected string $requestGrantType = '';
    protected ShipmentAddress $origin;
    protected ShipmentAddress $destination;
    private string $tracking_number;

    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
        $this->init();
    }

    private function init(): void
    {
        $this->setCredentials();
        $this->getAccessToken();
    }

    private function setCredentials(): void
    {
        $this->baseUrl = config('ups.sandbox') ? config('ups.sandbox_base_url') : config('ups.live_base_url');
        $this->clientId = config('ups.client_id');
        $this->clientSecret = config('ups.client_secret');
        $this->credentials = base64_encode("$this->clientId:$this->clientSecret");
    }

    private function setAccessToken($accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken(): bool
    {
        $this->requestGrantType = 'client_credentials';
        $response = $this->sendAuthRequest();
        if (!$response) return false;
        $result = json_decode($response, true);
        if (Arr::has($result, 'response') && Arr::has($result['response'], 'errors')) {
            if (count($result['response']['errors']) > 0) {
                activity()
                    ->performedOn(new Shipment())
                    ->causedBy(\request()->user())
                    ->withProperties([
                        'method' => __FUNCTION__,
                        'action' => 'UPS auth'
                    ])
                    ->log($result['response']['errors'][0]['code'] . ' | ' . $result['response']['errors'][0]['message']);
            }

            return false;
        }

        if (Arr::has($result, 'access_token')) $this->setAccessToken($result['access_token']);
        return true;
    }

    public function validateAddress()
    {
        $payload = [
            "XAVRequest" => [
                "AddressKeyFormat" => [
                    "ConsigneeName" => "RITZ CAMERA CENTERS-1749",
                    "BuildingName" => "Innoplex",
                    "AddressLine" => [
                        "26601 ALISO CREEK ROAD",
                        "STE D",
                        "ALISO VIEJO TOWN CENTER"
                    ],
                    "Region" => "ROSWELL,GA,30076-1521",
                    "PoliticalDivision2" => "ALISO VIEJO",
                    "PoliticalDivision1" => "CA",
                    "PostcodePrimaryLow" => "92656",
                    "PostcodeExtendedLow" => "1521",
                    "Urbanization" => "porto arundal",
                    "CountryCode" => "US"
                ]
            ]
        ];
    }

    public function calculateRate(): false|string
    {
        //TODO rate will come in
        $service_code = $this->serviceCode();
        if (!$service_code) return false;
        $weightUnit = $this->weightUnit(getCountry('id', $this->origin->country_id)->iso2);
        $shipment_item = ShipmentItem::where('shipment_id', $this->shipment->id)->first();
        try {
            $payload = [
                "RateRequest" => [
                    "Request" => [
                        "SubVersion" => "1703",
                        "TransactionReference" => [
                            "CustomerContext" => Str::uuid()
                        ]
                    ],
                    "Shipment" => [
                        "ShipmentRatingOptions" => [
                            "UserLevelDiscountIndicator" => "TRUE"
                        ],
                        "Shipper" => [
                            "Name" => "Parcels Mart Solutions",
                            "ShipperNumber" => config('ups.account'),
                            "Address" => [
                                "AddressLine" => "27, Sani Abacha Road, GRA",
                                "City" => "Port Harcourt",
                                "PostalCode" => "500001",
                                "CountryCode" => "NG"
                            ]
                        ],
                        "ShipFrom" => [
                            "Name" => $this->origin->contact_name,
                            "Address" => [
                                "AddressLine" => $this->origin->address_1,
                                "City" => getCity('id', $this->origin->city_id)->name,
                                "StateProvinceCode" => "",
                                "PostalCode" => $this->origin->postcode,
                                "CountryCode" => getCountry('id', $this->origin->country_id)->iso2
                            ]
                        ],
                        "ShipTo" => [
                            "Name" => $this->destination->contact_name,
                            "Address" => [
                                "AddressLine" => $this->destination->address_1,
                                "City" => getCity('id', $this->destination->city_id)->name,
                                "StateProvinceCode" => "",
                                "PostalCode" => $this->destination->postcode,
                                "CountryCode" => getCountry('id', $this->destination->country_id)->iso2
                            ]
                        ],
                        "Service" => [
                            "Code" => $service_code,
                            "Description" => "Express"
                        ],
                        "ShipmentTotalWeight" => [
                            "UnitOfMeasurement" => [
                                "Code" => $weightUnit,
                                "Description" => $weightUnit == "KGS" ? "Kilogram" : "Ponds"
                            ],
                            "Weight" => (string) $this->convertWeight($weightUnit, $shipment_item->weight)
                        ],
                        "Package" => [
                            "PackagingType" => [
                                "Code" => "02",
                                "Description" => "Package"
                            ],
                            "Dimensions" => [
                                "UnitOfMeasurement" => [
                                    "Code" =>  $this->metric($weightUnit)
                                ],
                                "Length" => (string) $this->convertMetric($weightUnit, $shipment_item->length),
                                "Width" => (string) $this->convertMetric($weightUnit, $shipment_item->width),
                                "Height" => (string) $this->convertMetric($weightUnit, $shipment_item->height)
                            ],
                            "PackageWeight" => [
                                "UnitOfMeasurement" => [
                                    "Code" => $weightUnit
                                ],
                                "Weight" => (string) $this->convertWeight($weightUnit, $shipment_item->weight)
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withToken($this->accessToken)->post("$this->baseUrl/api/rating/v2205/Rate", $payload);
            return ($response->status() == 200) ? $response->body() : false;
        } catch (\Throwable $throwable) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'UPS Shipment Rate'
                ])
                ->log($throwable->getMessage());
            return false;
        }
    }

    public function pickup(BookShipmentRequest $bookShipmentRequest, string $tracking_number)
    {
        $service_code = $this->serviceCode();
        if (!$service_code) return false;
        $weightUnit = $this->weightUnit(getCountry('id', $this->origin->country_id)->iso2);
        $shipment_item = ShipmentItem::where('shipment_id', $this->shipment->id)->first();
        $shipment_date = Carbon::create($bookShipmentRequest->shipment_date)->timezone('GMT+1');

        try {

            $payload = [
                "PickupCreationRequest" => [
                    "RatePickupIndicator" => "Y",
                    "Shipper" => [
                        "Account" => [
                            "AccountNumber" => config('ups.account'),
                            "AccountCountryCode" => "NG"
                        ]
                    ],
                    "PickupDateInfo" => [
//                        "CloseTime" => "1600",
//                        "ReadyTime" => $shipment_date->toTimeString(),
//                        "PickupDate" => $shipment_date->toDateString(),
                        "CloseTime" => "1400",
                        "ReadyTime" => "0500",
                        "PickupDate" => "20230928"
                    ],
                    "PickupAddress" => [
                        "CompanyName" => $this->origin->contact_name,
                        "ContactName" => $this->origin->contact_name,
                        "AddressLine" => $this->origin->address_1,
                        "City" => getCity('id', $this->origin->city_id)->name,
                        "CountryCode" => (string) getCountry('id', $this->origin->country_id)->iso2,
                        "PostalCode" => $this->origin->postcode,
                        "ResidentialIndicator" => "Y",
                        "Phone" => [
                            'Number' => $this->origin->contact_phone
                        ],
                    ],
                    "CustomerContext" => Str::uuid(),
                    "PaymentMethod" => "01",
                    "Request" => [
                        "RequestOption" => "SCHEDULE"
                    ],
                    "AlternateAddressIndicator" => 'Y',
                    "PickupPiece" => [
                        [
                            "ServiceCode" => '001',
                            "DestinationCountryCode" => getCountry('id', $this->destination->country_id)->iso2,
                            "Quantity" => (string) $shipment_item->quantity,
                            "ContainerCode" => "01"
                        ]
                    ],

                    "TotalWeight" => [
                        "Weight" => (string) $this->convertWeight($weightUnit, $shipment_item->weight),
                        "UnitOfMeasurement" => $weightUnit
                    ],
                    //"ReferenceNumber" => Str::uuid(),
                    "Notification" => [
                        "ConfirmationEmailAddress" => $this->origin->contact_email,
                        "UndeliverableEmailAddress" => $this->origin->contact_email
                    ],
//                    "TrackingData" => [
//                        [
//                            "TrackingNumber" => $tracking_number
//                        ]
//                    ],

                ]
            ];

            //dd($payload);
            $response = Http::withToken($this->accessToken)->post("$this->baseUrl/api/pickupcreation/v2205/pickup", $payload);
            if ($response->status() != 200) {
                throw ValidationException::withMessages(['message' => 'Unable to book shipment. Please try again later (P)']);
            }
            return $response->body();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @throws ValidationException
     */
    public function bookShipment(ShipmentItem $shipmentItem, BookShipmentRequest $bookShipmentRequest, ShippingRateLog $shippingRateLog)
    {
        $service_code = $this->serviceCode();
        if (!$service_code) return false;
        $weightUnit = $this->weightUnit(getCountry('id', $this->origin->country_id)->iso2);
        $shipment_item = ShipmentItem::where('shipment_id', $this->shipment->id)->first();
        $shipment_date = Carbon::create($bookShipmentRequest->shipment_date)->timezone('GMT+1');
        $shipment_date = str_replace(' ', 'T', $shipment_date->toDateTimeString()) . " GMT+01:00";

        $payload = [
            "ShipmentRequest" => [
                "Request" => [
                    "SubVersion" => "2205",
                    "RequestOption" => "nonvalidate",
                    "TransactionReference" => [
                        "CustomerContext" => Str::uuid()
                    ]
                ],
                "Shipment" => [
                    "Description" => "Parcel/Document Shipment",
                    "Shipper" => config('ups.Shipper'),
                    "ShipTo" => [
                        "Name" => $this->destination->contact_name,
                        "AttentionName" => $this->destination->contact_name,
                        "Phone" => [
                            "Number" => $this->destination->contact_phone
                        ],
                        "Address" => [
                            "AddressLine" => $this->destination->address_1,
                            "City" => getCity('id', $this->destination->city_id)->name,
                            "StateProvinceCode" => "",
                            "PostalCode" => $this->destination->postcode,
                            "CountryCode" => getCountry('id', $this->destination->country_id)->iso2
                        ]
                    ],
                    "ShipFrom" => [
                        "Name" => $this->origin->contact_name,
                        "AttentionName" => $this->origin->contact_name,
                        "Phone" => [
                            "Number" => $this->origin->contact_phone
                        ],
                        "Address" => [
                            "AddressLine" => $this->origin->address_1,
                            "City" => getCity('id', $this->origin->city_id)->name,
                            "StateProvinceCode" => "",
                            "PostalCode" => $this->origin->postcode,
                            "CountryCode" => getCountry('id', $this->origin->country_id)->iso2
                        ]
                    ],
                    "PaymentInformation" => [
                        "ShipmentCharge" => [
                            "Type" => "01",
                            "BillShipper" => [
                                "AccountNumber" => config('ups.account')
                            ]
                        ]
                    ],
                    "Service" => [
                        "Code" => $service_code,
                        "Description" => "Express"
                    ],
                    "Package" => [
                        "Description" => "Package",
                        "Packaging" => [
                            "Code" => "02",
                            "Description" => "Package"
                        ],
                        "Dimensions" => [
                            "UnitOfMeasurement" => [
                                "Code" =>  $this->metric($weightUnit)
                            ],
                            "Length" => (string) $this->convertMetric($weightUnit, $shipment_item->length),
                            "Width" => (string) $this->convertMetric($weightUnit, $shipment_item->width),
                            "Height" => (string) $this->convertMetric($weightUnit, $shipment_item->height)
                        ],
                        "PackageWeight" => [
                            "UnitOfMeasurement" => [
                                "Code" => $weightUnit
                            ],
                            "Weight" => (string) $this->convertWeight($weightUnit, $shipment_item->weight)
                        ]
                    ],
                ],
                "LabelSpecification" => [
                    "LabelImageFormat" => [
                        "Code" => "GIF",
                        "Description" => "GIF"
                    ],
                    "HTTPUserAgent" => "Mozilla/4.5"
                ]
            ]
        ];
        //dd($payload);
        $response = Http::withToken($this->accessToken)->post("$this->baseUrl/api/shipments/v2205/ship", $payload);
        if ($response->status() != 200) {
            throw ValidationException::withMessages(['message' => 'Unable to book shipment. Please try again later']);
        }
        /*UpsShipme::create([
            'shipment_id' => $shipment->id,
            'shipment_rate_log_id' => $shippingRateLog->id,
            'shipment_tracking_number' => $result['shipmentTrackingNumber'],
            'tracking_url' => $result['trackingUrl'],
            'cancel_pickup_url' => $result['cancelPickupUrl'],
            'dispatch_confirmation_number' => $result['dispatchConfirmationNumber'],
            'document_content' => json_encode($result['documents']),
            'package_details' => json_encode($result['packages'])
        ]);*/

        $result = json_decode($response->body(), true);
        $shipment_response = $result['ShipmentResponse']['Response']['ResponseStatus'];
        if ($shipment_response['Code'] == 1 && $shipment_response['Description'] == 'Success') {
            $shipment_result = $result['ShipmentResponse']['ShipmentResults'];
            $tracking_number = $shipment_result['PackageResults']['TrackingNumber'];
            $this->shipment->number = $tracking_number;
            $this->shipment->save();

            $ups_shipment_log = new UpsShipmentLog;
            $ups_shipment_log->shipment_id = $this->shipment->id;
            $ups_shipment_log->tracking_number = $tracking_number;
            $ups_shipment_log->identification_number = $shipment_result['ShipmentIdentificationNumber'];
            $ups_shipment_log->document_content = json_encode($shipment_result['PackageResults']['ShippingLabel']);
            $ups_shipment_log->reference = $result['ShipmentResponse']['Response']['TransactionReference']['CustomerContext'];
            $ups_shipment_log->save();

            $pickup = $this->pickup($bookShipmentRequest, $tracking_number);
            if (!$pickup) {
                throw ValidationException::withMessages(['message' => 'Unable to book shipment. Please try again later']);
            }

            $pickup_result = json_decode($pickup, true);
            $pickup_number = $pickup_result['PickupCreationResponse']['PRN'];
            $shippingRateLog->pickup_number = $pickup_number;
            $shippingRateLog->save();
            $this->shipment->pickup_number = $pickup_number;
            $this->shipment->save();
            return true;
        }

        return ($response->status() == 200) ? $response->body() : false;
    }

    private function serviceCode(): bool|string
    {
        $this->origin = ShipmentAddress::where([
            'shipment_id' => $this->shipment->id,
            'type' => 'origin'])->first();
        $this->destination = ShipmentAddress::where([
            'shipment_id' => $this->shipment->id,
            'type' => 'destination'])->first();
        $type = $this->shipmentType();
        if (!$type) return false;
        return ($type == "DOMESTIC") ? '11' : '65';
    }

    private function productCode($origin, $destination, $shipmentItem): bool|string
    {
        $product_code = '';
        $type = $this->shipmentType($origin, $destination);
        if (!$type) return false;
        if ($type == "DOMESTIC") $product_code = 'N';
        if ($type == "IMPORT" || $type == 'EXPORT') {
            $category = ItemCategory::find($shipmentItem['category'])->name ?? "Parcel";
            $product_code = $category == substr($category, 0 ,1);
        }
        return $product_code;
    }

    private function shipmentType(): string
    {
        $origin_country = getCountry('id', $this->origin->country_id)->iso2;
        $destination_country = getCountry('id', $this->destination->country_id)->iso2;

        $type = '';
        if ($origin_country == 'NG') {
            if ($destination_country == 'NG') $type = 'DOMESTIC';
            if ($destination_country != 'NG') $type = 'EXPORT';
        }

        if ($origin_country != 'NG') {
            if ($destination_country == 'NG') $type = 'IMPORT';
            if ($destination_country != 'NG') $type = 'INTERNATIONAL';
        }
        
        return $type;
    }

    private function weightUnit($countryCode): string
    {
        return in_array($countryCode, ['US', 'UK']) ? "LBS" : "KGS";
    }

    private function kgToLbs($kg): float
    {
        return $kg * 2.20462;
    }

    private function cmToIn($cm): float
    {
        return $cm * 0.393701;
    }

    private function metric($weightUnit): string
    {
        return  ($weightUnit == 'LBS') ? "IN" : "CM";
    }

    private function convertMetric($weighUnit, $value)
    {
        return ($this->metric($weighUnit) == 'IN') ? $this->cmToIn($value) : $value;
    }

    private function convertWeight($weighUnit, $value)
    {
        return ($weighUnit == 'LBS') ? $this->kgToLbs($value) : $value;
    }

    private function sendAuthRequest(): bool|string
    {
        try {
            $url = $this->baseUrl . '/security/v1/oauth/token';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'accept: application/json',
                "x-merchant-id: $this->clientId",
                "Authorization: Basic $this->credentials",
                'Content-Type: application/x-www-form-urlencoded',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type={$this->requestGrantType}");
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } catch (\Throwable $throwable) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'UPS auth'
                ])
                ->log($throwable->getMessage());
            return false;
        }
    }
}