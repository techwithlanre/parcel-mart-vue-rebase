<?php

namespace App\Services;

use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Http\Requests\TrackShipmentRequest;
use App\Models\ItemCategory;
use App\Models\Shipment;
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
    public Request|TrackShipmentRequest|BookShipmentRequest|CreateShipmentRequest $request;
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

    public function __construct(CreateShipmentRequest|BookShipmentRequest|TrackShipmentRequest|Request $request)
    {
        $this->request = $request;
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

    public function calculateRate(): false|string
    {
        $product_code = $this->productCode($this->request->origin, $this->request->destination, $this->request->shipment);
        if (!$product_code) return false;
        $type = $this->shipmentType($this->request->origin, $this->request->destination);
        if (!$type) return false;
        $weightUnit = $this->weightUnit(getCountry('id', $this->request->destination['country'])->iso2);
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
                            "Name" => $this->request->destination['contact_name'],
                            "Address" => [
                                "AddressLine" => $this->request->destination['address_1'],
                                "City" => getCity('id', $this->request->destination['city'])->name,
                                "StateProvinceCode" => "",
                                "PostalCode" => $this->request->destination['postcode'],
                                "CountryCode" => getCountry('id', $this->request->destination['country'])->iso2
                            ]
                        ],
                        /*"ShipFrom" => [
                            "Name" => $this->request->origin['contact_name'],
                            "Address" => [
                                "AddressLine" => $this->request->origin['address_1'],
                                "City" => getCity('id', $this->request->origin['city'])->name,
                                "StateProvinceCode" => "",
                                "PostalCode" => $this->request->origin['postcode'],
                                "CountryCode" => getCountry('id', $this->request->origin['country'])->iso2
                            ]
                        ],*/
                        "ShipTo" => [
                            "Name" => $this->request->origin['contact_name'],
                            "Address" => [
                                "AddressLine" => $this->request->origin['address_1'],
                                "City" => "New York",
                                "StateProvinceCode" => "",
                                "PostalCode" => "95113",
                                "CountryCode" => "US"
                            ]
                        ],
                        "Service" => [
                            "Code" => "08",
                            "Description" => ""
                        ],
                        "ShipmentTotalWeight" => [
                            "UnitOfMeasurement" => [
                                "Code" => $weightUnit,
                                "Description" => ""
                            ],
                            "Weight" => "10"
                        ],
                        "Package" => [
                            "PackagingType" => [
                                "Code" => "02",
                                "Description" => "Package"
                            ],
                            "Dimensions" => [
                                "UnitOfMeasurement" => [
                                    "Code" => $this->metric($weightUnit)
                                ],
                                "Length" => $this->convertMetric($weightUnit, $this->request->shipment['length']),
                                "Width" => $this->convertMetric($weightUnit, $this->request->shipment['width']),
                                "Height" => $this->convertMetric($weightUnit, $this->request->shipment['height'])
                            ],
                            "PackageWeight" => [
                                "UnitOfMeasurement" => [
                                    "Code" => $weightUnit
                                ],
                                "Weight" => $this->convertWeight($weightUnit, $this->request->shipment['weight'])
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

    private function shipmentType($origin, $destination): string
    {
        $origin_country = getCountry('id', $origin['country'])->iso2;
        $destination_country = getCountry('id', $destination['country'])->iso2;

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