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
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
    public string $clientId = 'SHcfIjA9d3Gu7iLAVEGi4uKnYSRYalW7GoUARK1K4EKY4YkL';
    public string $clientSecret = '3hDpomnTOogSpubXqzgSa6yRn3KTyqKCO7uUqCHpcnzRmVXskdhlRaWUs2gGyqOV';
    protected string $credentials = '';
    public string $accessToken = '';
    protected string $requestGrantType = '';
    protected ShipmentAddress $origin;
    protected ShipmentAddress $destination;

    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
        $this->init();
    }

    private function init(): void
    {
        $this->setBaseUrl();;
        $this->setCredentials();
        $this->getAccessToken();
    }

    public function setBaseUrl(): void
    {
        $this->baseUrl = config('ups.sandbox') ? 'https://wwwcie.ups.com' : 'https://onlinetools.ups.con';
    }

    private function setCredentials(): void
    {
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
        }

        if (Arr::has($result, 'access_token')) $this->setAccessToken($result['access_token']);
        return true;
    }

    public function validateAddress()
    {
        //dd($this->accessToken);
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
                            "ShipperNumber" => "A1226Y",
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

    public function pickup()
    {
        $service_code = $this->serviceCode();
        if (!$service_code) return false;
        $weightUnit = $this->weightUnit(getCountry('id', $this->origin->country_id)->iso2);
        $shipment_item = ShipmentItem::where('shipment_id', $this->shipment->id)->first();
        $jayParsedAry = [
            "PickupCreationRequest" => [
                "CustomerContext" => Str::uuid(),
                "Request" => [
                    "RequestOption" => "SCHEDULE"
                ],
                "Shipper" => [
                    "Name" => "Parcels Mart Solutions",
                    "ShipperNumber" => "A1226Y",
                    "Address" => [
                        "AddressLine" => "27, Sani Abacha Road, GRA",
                        "City" => "Port Harcourt",
                        "PostalCode" => "500001",
                        "CountryCode" => "NG"
                    ]
                ],
                "PickupDateInfo" => [
                    "CloseTime" => "HH:MM:SS",
                    "ReadyTime" => "HH:MM:SS",
                    "PickupDate" => "YYYY-MM-DD"
                ],
                "PickupAddress" => [
                    "CompanyName" => "Pickup Company Name",
                    "ContactName" => "Pickup Contact Name",
                    "PhoneNumber" => "Pickup Phone Number",
                    "Address" => [
                        "AddressLine" => "Pickup Address Line",
                        "City" => "Pickup City",
                        "StateProvinceCode" => "Pickup State",
                        "PostalCode" => "Pickup Postal Code",
                        "CountryCode" => "Pickup Country Code"
                    ]
                ],
                "Package" => [
                    [
                        "TrackingNumber" => "Tracking Number (if applicable)",
                        "Count" => "Number of Packages",
                        "Weight" => "Package Weight (e.g., 10.0 LB)"
                    ]
                ],
                "TotalWeight" => "Total Weight of all packages (e.g., 30.0 LB)"
            ]
        ];


    }

    public function bookShipment(ShipmentItem $shipmentItem, BookShipmentRequest $bookShipmentRequest)
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
                        "CustomerContext" => ""
                    ]
                ],
                "Shipment" => [
                    "Description" => "Parcel/Document Shipment",
                    "Shipper" => [
                        "Name" => "Parcels Mart Solution",
                        "AttentionName" => "Parcels Mart Solutions",
                        "TaxIdentificationNumber" => "",
                        "Phone" => [
                            "Number" => "08136437952",
                            "Extension" => ""
                        ],
                        "ShipperNumber" => "A1226Y",
                        "Address" => [
                            "AddressLine" => "27, Sani Abacha Road, GRA",
                            "City" => "Port Harcourt",
                            "PostalCode" => "500001",
                            "CountryCode" => "NG"
                        ]
                    ],
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
                                "AccountNumber" => "A1226Y"
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

        $response = Http::withToken($this->accessToken)->post("$this->baseUrl/api/shipments/v2205/ship", $payload);
        dd($response->body());
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
            $url = 'https://wwwcie.ups.com/security/v1/oauth/token';
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