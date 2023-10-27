<?php

namespace App\Services;

use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Http\Requests\TrackShipmentRequest;
use App\Models\DhlShipmentLog;
use App\Models\InsuranceOption;
use App\Models\ItemCategory;
use App\Models\Shipment;
use App\Models\ShipmentAddress;
use App\Models\ShipmentItem;
use App\Models\ShippingRateLog;
use Carbon\Carbon;
use Cubes\MyDhl\RateRequest\RequestedShipment\Ship;
use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DHLServices
{
    protected Shipment $shipment;
    protected String $env;
    protected String $base_url;
    protected String $account_number;
    protected String $import_account_number;
    protected String $username;
    protected String $password;
    protected array $calculateRatePayload;
    protected array $bookShipmentPayload;

    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
        $this->initialize();
    }

    private function initialize(): void
    {
        $this->env = config('dhl.ENV');
        $this->base_url = config('dhl.'.$this->env.'.baseUrl');
        $this->account_number = config('dhl.'.$this->env.'.accountNumber');
        $this->import_account_number = config('dhl.'.$this->env.'.importAccountNumber');
        $this->username = config('dhl.'.$this->env.'.username');
        $this->password = config('dhl.'.$this->env.'.password');
    }

    public function calculateRate()
    {
        $calculate = $this->calculateRatePayload();
        if(!$calculate) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'DHL calculate rate'
                ])
                ->log('Could not set rate payload');
            return false;
        }

        $response = Http::withBasicAuth($this->username, $this->password)
            ->withHeaders(['Content-Type: application/json'])->post($this->base_url.'/rates', $this->calculateRatePayload);
        //dd($response->body());
        return $response->body();
    }

    private function calculateRatePayload()
    {
        $origin = ShipmentAddress::where(['shipment_id' => $this->shipment->id,'type' => 'origin'])->first();
        $destination = ShipmentAddress::where(['shipment_id' => $this->shipment->id,'type' => 'destination'])->first();
        $product_code = $this->productCode($origin, $destination, $this->shipment);
        if (!$product_code) return false;
        $type = $this->isInternational($origin, $destination);
        if (!$type) return false;
        $account_number = $type == "I" ? $this->import_account_number : $this->account_number;
        $date = new DateTime();
        $shipment_date = $date->modify('+1 weekday')->format('Y-m-d 10:00:00');
        $shipment_date = str_replace(' ', 'T', $shipment_date) . " GMT+0100";
        $item = ShipmentItem::where('shipment_id', $this->shipment->id)->first();
        $this->calculateRatePayload = [
            "plannedShippingDateAndTime" => $shipment_date,
            "productCode" => $product_code,
            "payerCountryCode" => "NG",
            "unitOfMeasurement" => "metric",
            "isCustomsDeclarable" => $type == "I",
            "nextBusinessDay" => true,
            "accounts" => [
                [
                    "number" => $account_number,
                    "typeCode" => "shipper"
                ]
            ],
            "customerDetails" => [
                "shipperDetails" => [
                    "addressLine1" => substr($origin->address_1, 0, 45),
                    "postalCode" => $origin->postcode,
                    "cityName" => getCity('id', $origin->city_id)->name,
                    "countryCode" => getCountry('id', $origin->country_id)->iso2
                ],
                "receiverDetails" => [
                    "addressLine1" => substr($destination->address_1, 0, 45),
                    "postalCode" => $destination->postcode,
                    "cityName" => getCity('id', $destination->city_id)->name,
                    "countryCode" => getCountry('id', $destination->country_id)->iso2
                ]
            ],
            "monetaryAmount" => [
                [
                    "typeCode" => "declaredValue",
                    "value" => (float) $item->value,
                    "currency" => "NGN"
                ]
            ],
            "packages" => [
                [
                    "weight" => (float) $item->weight,
                    "dimensions" => [
                        "length" => (float) $item->length,
                        "width" => (float) $item->width,
                        "height" => (float) $item->height
                    ]
                ]
            ]
        ];

        return true;
    }

    /**
     * @throws ValidationException
     */
    public function bookShipment(ShipmentItem $shipmentItem, InsuranceOption $insuranceOption, ShippingRateLog $shippingRateLog, BookShipmentRequest $bookShipmentRequest)
    {
        $shipment = $this->shipment;
        $payload = $this->bookShipmentPayload($shipment, $shipmentItem, $insuranceOption, $shippingRateLog, $bookShipmentRequest);
        if (!$payload)  throw ValidationException::withMessages(['message' => 'Unable to book shipment. Please try again later']);

        $result = $this->sendBookShipmentRequest();
        if (!$result) throw ValidationException::withMessages(['message' => 'Unable to book shipment. Please try again later']);

        DhlShipmentLog::create([
            'shipment_id' => $shipment->id,
            'shipment_rate_log_id' => $shippingRateLog->id,
            'shipment_tracking_number' => $result['shipmentTrackingNumber'],
            'tracking_url' => $result['trackingUrl'],
            'cancel_pickup_url' => $result['cancelPickupUrl'],
            'dispatch_confirmation_number' => $result['dispatchConfirmationNumber'],
            'document_content' => json_encode($result['documents']),
            'package_details' => json_encode($result['packages'])
        ]);

        $shippingRateLog->pickup_number = $result['dispatchConfirmationNumber'];
        $shippingRateLog->save();
        $shipment->number = $result['shipmentTrackingNumber'];
        $shipment->pickup_number = $result['dispatchConfirmationNumber'];
        $shipment->save();
        return true;
    }

    /**
     * @throws ValidationException
     */
    public function bookPickup(Shipment $shipment, ShipmentItem $shipmentItem, InsuranceOption $insuranceOption, ShippingRateLog $shippingRateLog, BookShipmentRequest $bookShipmentRequest)
    {
        $payload = $this->bookShipmentPayload($shipment, $shipmentItem, $insuranceOption, $shippingRateLog, $bookShipmentRequest);
        if (!$payload)  throw ValidationException::withMessages(['message' => 'Unable to book shipment. Please try again later']);

        $result = $this->sendBookShipmentRequest();
        if (!$result) throw ValidationException::withMessages(['message' => 'Unable to book shipment. Please try again later']);

        DhlShipmentLog::create([
            'shipment_id' => $shipment->id,
            'shipment_rate_log_id' => $shippingRateLog->id,
            'shipment_tracking_number' => $result['shipmentTrackingNumber'],
            'tracking_url' => $result['trackingUrl'],
            'document_content' => json_encode($result['documents']),
            'package_details' => json_encode($result['packages'])
        ]);

        return true;
    }

    private function isInternational($origin, $destination): string
    {

        $origin_country = getCountry('id', $origin->country_id)->iso2;
        $destination_country = getCountry('id', $destination->country_id)->iso2;

        $type = '';
        //if ($origin_country !== 'NG' || $destination_country !== 'NG') return false;
        if ($origin_country == 'NG' && $destination_country == 'NG') $type = 'D';
        if ($origin_country == 'NG' && $destination_country != 'NG') $type = 'I';
        if ($origin_country != 'NG' && $destination_country == 'NG') $type = 'I';
        return $type;
    }

    private function productCode($origin, $destination, $shipmentItem)
    {
        $product_code = '';
        $type = $this->isInternational($origin, $destination);
        if (!$type) return false;
        if ($type == "D") $product_code = 'N';
        if ($type == "I")  {
            $category = ItemCategory::find($shipmentItem['category'])->name ?? "Parcel";
            if ($category == 'Parcel') $product_code = 'P';
            if ($category == 'Document') $product_code = 'D';
        }

        return $product_code;
    }

    private function bookShipmentPayload(Shipment $shipment, ShipmentItem $shipmentItem, InsuranceOption $insuranceOption, ShippingRateLog $shippingRateLog, BookShipmentRequest $bookShipmentRequest)
    {
        $origin = ShipmentAddress::where(['shipment_id' => $this->shipment->id,'type' => 'origin'])->first();
        $destination = ShipmentAddress::where(['shipment_id' => $this->shipment->id,'type' => 'destination'])->first();
        $product_code = $this->productCode($origin, $destination, $shipment);
        if (!$product_code) return false;
        $type = $this->isInternational($origin, $destination);
        if (!$type) return false;
        $account_number = $type == "I" ? $this->import_account_number : $this->account_number;
        $shipment_date = Carbon::create($bookShipmentRequest->shipment_date)->timezone('GMT+1');
        $shipment_date = str_replace(' ', 'T', $shipment_date->toDateTimeString()) . " GMT+01:00";

        $this->bookShipmentPayload = [
            "plannedShippingDateAndTime" => $shipment_date,
            "productCode" => $product_code,
            "pickup" =>  [
                "isRequested" => true,
                "closeTime" => "18:00",
                "location" => "reception",
            ],
            "outputImageProperties" => [
                "allDocumentsInOneImage" => true,
                "encodingFormat" => "pdf",
                "imageOptions" => [
                    [
                        "templateName" => "ECOM26_84_A4_001",
                        "typeCode" => "label"
                    ],
                    [
                        "templateName" => "ARCH_8X4_A4_002",
                        "isRequested" => true,
                        "typeCode" => "waybillDoc",
                        "hideAccountNumber" => true
                    ]
                ]
            ],
            "accounts" => [
                [
                    "number" => $account_number,
                    "typeCode" => "shipper"
                ]
            ],
            "customerDetails" => [
                "shipperDetails" => [
                    'postalAddress' => [
                        "postalCode" => $origin['postcode'] ?? '',
                        "cityName" => getCity('id', $origin->city_id)->name,
                        "countryCode" => getCountry('id', $origin->country_id)->iso2,
                        "addressLine1" => substr($origin->address_1, 0, 45),
                        "addressLine2" => !empty($origin->address_2) ? $origin->address_2 : $origin->landmark,
                        "addressLine3" => $origin->landmark,
                        "countyName" => getState('id', $origin->state_id)->name,
                    ],
                    'contactInformation' => [
                        "email" => $origin->contact_email,
                        "phone" => $origin->contact_phone,
                        "companyName" => $origin->company_name ?? $origin->contact_name,
                        "fullName" => $origin->contact_name
                    ],
                    "typeCode" => "business"
                ],
                "receiverDetails" => [
                    'postalAddress' => [
                        "postalCode" => $destination->postcode ?? '',
                        "cityName" => getCity('id', $destination->city_id)->name,
                        "countryCode" => getCountry('id', $destination->country_id)->iso2,
                        "addressLine1" => substr($destination->address_1, 0, 45),
                        "addressLine2" => !empty($destination->address_2) ? $destination->address_2 : $destination->landmark,
                        "addressLine3" => $destination->landmark,
                        "countyName" => getState('id', $destination->state_id)->name,
                    ],
                    'contactInformation' => [
                        "email" => $destination->contact_email,
                        "phone" => $destination->contact_phone,
                        "companyName" => $destination->company_name ?? $destination->contact_name,
                        "fullName" => $destination->contact_name
                    ],
                    "typeCode" => "business"
                ]
            ],
            "content" => [
                "packages" => [
                    [
                        'weight' => (float) $shipmentItem->weight,
                        'dimensions' => [
                            'length' => (float) $shipmentItem->length,
                            'width' => (float) $shipmentItem->width,
                            'height' => (float) $shipmentItem->height,
                        ],
                        'description' => $shipment->description ?? 'Package Description'
                    ]
                ],
                "isCustomsDeclarable" => $type == 'I',
                "description" => $shipmentItem->description,
                "incoterm" => "DAP",
                "unitOfMeasurement" => "metric",
                "declaredValueCurrency" => "NGN",
                "declaredValue" => (float) $shipmentItem->value
            ]
        ];

        if ($type == 'I') {
            $this->bookShipmentPayload['content']['exportDeclaration'] = [
                "lineItems" => [
                    [
                        "number" => $shipmentItem->quantity,
                        "quantity" => [
                            "unitOfMeasurement" => "PCS",
                            "value" => $shipmentItem->quantity
                        ],
                        "price" => (float) $shipmentItem->value,
                        "description" => $shipmentItem->description,
                        "weight" => [
                            "netValue" => (float) $shipmentItem->weight,
                            "grossValue" => (float) $shipmentItem->weight
                        ],
                        "commodityCodes" => [
                            [
                                "typeCode" => "outbound",
                                "value" => "123456.0044.9954"
                            ]
                        ],
                        "exportReasonType" => "permanent",
                        "manufacturerCountry" => "NG"
                    ]
                ],
                "exportReason" => "Permanent",
                "invoice" => [
                    "number" => $bookShipmentRequest->invoice_number,
                    "date" => date('Y-m-d', strtotime($bookShipmentRequest->invoice_date))
                ],
                "placeOfIncoterm" => getCity('id', $destination->city_id)->name,
                "exportReasonType" => "permanent",
                "shipmentType" => "commercial"
            ];
        }


        return true;
    }

    private function sendBookShipmentRequest()
    {
        //dd($this->base_url.'/shipments');

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'Message-Reference: ' . Str::uuid(),
                    'Content-Type: application/json',
                    'Accept: application/json',
                ])->post($this->base_url.'/shipments', $this->bookShipmentPayload);
            $result = json_decode($response->body(), true);
            //dd($result);
            if (!isset($result['shipmentTrackingNumber'])) {
                activity()
                    ->performedOn(new Shipment())
                    ->causedBy(request()->user())
                    ->withProperties([
                        'method' => __FUNCTION__,
                        'action' => 'DHL book shipment'
                    ])->log($response);

                throw ValidationException::withMessages(['message' => 'Unable to book shipment. Please try again later']);
            }
            return $result;
        } catch (\Throwable $e) {
            activity()
                ->performedOn(new Shipment())
                ->causedBy(\request()->user())
                ->withProperties([
                    'method' => __FUNCTION__,
                    'action' => 'DHL book shipment'
                ])
                ->log($e->getMessage());
            throw ValidationException::withMessages(['message' => 'Unable to book shipment. Please try again later']);
        }
    }

    public function trackShipment(DhlShipmentLog $dhlShipmentLog)
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'Message-Reference: ' . Str::uuid(),
                    'Content-Type: application/json',
                    'Accept: application/json',
                ])->get($dhlShipmentLog->tracking_url);
            if ($response->status() == 200)  return $response->body();
            return false;
        } catch (\Throwable $e) {
            $response = $e->getMessage();
            return redirect()->back()->with('error', 'We are working on tracking info. Please check back later');
        }
    }

    public function hello()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "$this->base_url.'/shipments?strictValidation=false&bypassPLTError=false&validateDataOnly=false'",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{'plannedShippingDateAndTime':'2023-07-15T14:00:31GMT+01:00','pickup':{'isRequested':false,'closeTime':'18:00','location':'reception','specialInstructions':[{'value':'please ring door bell','typeCode':'TBD'}],'pickupDetails':{'postalAddress':{'postalCode':'14800','cityName':'Prague','countryCode':'CZ','provinceCode':'CZ','addressLine1':'V Parku 2308/10','addressLine2':'addres2','addressLine3':'addres3','countyName':'Central Bohemia','provinceName':'Central Bohemia','countryName':'Czech Republic'},'contactInformation':{'email':'that@before.de','phone':'+1123456789','mobilePhone':'+60112345678','companyName':'Company Name','fullName':'John Brew'},'registrationNumbers':[{'typeCode':'VAT','number':'CZ123456789','issuerCountryCode':'CZ'}],'bankDetails':[{'name':'Russian Bank Name','settlementLocalCurrency':'RUB','settlementForeignCurrency':'USD'}],'typeCode':'business'},'pickupRequestorDetails':{'postalAddress':{'postalCode':'14800','cityName':'Prague','countryCode':'CZ','provinceCode':'CZ','addressLine1':'V Parku 2308/10','addressLine2':'addres2','addressLine3':'addres3','countyName':'Central Bohemia','provinceName':'Central Bohemia','countryName':'Czech Republic'},'contactInformation':{'email':'that@before.de','phone':'+1123456789','mobilePhone':'+60112345678','companyName':'Company Name','fullName':'John Brew'},'registrationNumbers':[{'typeCode':'VAT','number':'CZ123456789','issuerCountryCode':'CZ'}],'bankDetails':[{'name':'Russian Bank Name','settlementLocalCurrency':'RUB','settlementForeignCurrency':'USD'}],'typeCode':'business'}},'productCode':'D','localProductCode':'D','getRateEstimates':false,'accounts':[{'typeCode':'shipper','number':'123456789'}],'valueAddedServices':[{'serviceCode':'II','value':100,'currency':'GBP','method':'cash','dangerousGoods':[{'contentId':'908','dryIceTotalNetWeight':12,'customDescription':'1 package Lithium ion batteries in compliance with Section II of P.I. 9661','unCodes':[1234]}]}],'outputImageProperties':{'printerDPI':300,'customerBarcodes':[{'content':'barcode content','textBelowBarcode':'text below barcode','symbologyCode':'93'}],'customerLogos':[{'fileFormat':'PNG','content':'base64 encoded image'}],'encodingFormat':'pdf','imageOptions':[{'typeCode':'label','templateName':'ECOM26_84_001','isRequested':true,'hideAccountNumber':false,'numberOfCopies':1,'invoiceType':'commercial','languageCode':'eng','languageCountryCode':'br','encodingFormat':'png','renderDHLLogo':false,'fitLabelsToA4':false,'labelFreeText':'string','labelCustomerDataText':'string'}],'splitTransportAndWaybillDocLabels':true,'allDocumentsInOneImage':true,'splitDocumentsByPages':true,'splitInvoiceAndReceipt':true,'receiptAndLabelsInOneImage':true},'customerReferences':[{'value':'Customer reference','typeCode':'CU'}],'identifiers':[{'typeCode':'shipmentId','value':'1234567890','dataIdentifier':'00'}],'customerDetails':{'shipperDetails':{'postalAddress':{'postalCode':'14800','cityName':'Prague','countryCode':'CZ','provinceCode':'CZ','addressLine1':'V Parku 2308/10','addressLine2':'addres2','addressLine3':'addres3','countyName':'Central Bohemia','provinceName':'Central Bohemia','countryName':'Czech Republic'},'contactInformation':{'email':'that@before.de','phone':'+1123456789','mobilePhone':'+60112345678','companyName':'Company Name','fullName':'John Brew'},'registrationNumbers':[{'typeCode':'VAT','number':'CZ123456789','issuerCountryCode':'CZ'}],'bankDetails':[{'name':'Russian Bank Name','settlementLocalCurrency':'RUB','settlementForeignCurrency':'USD'}],'typeCode':'business'},'receiverDetails':{'postalAddress':{'postalCode':'14800','cityName':'Prague','countryCode':'CZ','provinceCode':'CZ','addressLine1':'V Parku 2308/10','addressLine2':'addres2','addressLine3':'addres3','countyName':'Central Bohemia','provinceName':'Central Bohemia','countryName':'Czech Republic'},'contactInformation':{'email':'that@before.de','phone':'+1123456789','mobilePhone':'+60112345678','companyName':'Company Name','fullName':'John Brew'},'registrationNumbers':[{'typeCode':'VAT','number':'CZ123456789','issuerCountryCode':'CZ'}],'bankDetails':[{'name':'Russian Bank Name','settlementLocalCurrency':'RUB','settlementForeignCurrency':'USD'}],'typeCode':'business'},'buyerDetails':{'postalAddress':{'postalCode':'14800','cityName':'Prague','countryCode':'CZ','provinceCode':'CZ','addressLine1':'V Parku 2308/10','addressLine2':'addres2','addressLine3':'addres3','countyName':'Central Bohemia','provinceName':'Central Bohemia','countryName':'Czech Republic'},'contactInformation':{'email':'buyer@domain.com','phone':'+44123456789','mobilePhone':'+42123456789','companyName':'Customer Company Name','fullName':'Mark Companer'},'registrationNumbers':[{'typeCode':'VAT','number':'CZ123456789','issuerCountryCode':'CZ'}],'bankDetails':[{'name':'Russian Bank Name','settlementLocalCurrency':'RUB','settlementForeignCurrency':'USD'}],'typeCode':'business'},'importerDetails':{'postalAddress':{'postalCode':'14800','cityName':'Prague','countryCode':'CZ','provinceCode':'CZ','addressLine1':'V Parku 2308/10','addressLine2':'addres2','addressLine3':'addres3','countyName':'Central Bohemia','provinceName':'Central Bohemia','countryName':'Czech Republic'},'contactInformation':{'email':'that@before.de','phone':'+1123456789','mobilePhone':'+60112345678','companyName':'Company Name','fullName':'John Brew'},'registrationNumbers':[{'typeCode':'VAT','number':'CZ123456789','issuerCountryCode':'CZ'}],'bankDetails':[{'name':'Russian Bank Name','settlementLocalCurrency':'RUB','settlementForeignCurrency':'USD'}],'typeCode':'business'},'exporterDetails':{'postalAddress':{'postalCode':'14800','cityName':'Prague','countryCode':'CZ','provinceCode':'CZ','addressLine1':'V Parku 2308/10','addressLine2':'addres2','addressLine3':'addres3','countyName':'Central Bohemia','provinceName':'Central Bohemia','countryName':'Czech Republic'},'contactInformation':{'email':'that@before.de','phone':'+1123456789','mobilePhone':'+60112345678','companyName':'Company Name','fullName':'John Brew'},'registrationNumbers':[{'typeCode':'VAT','number':'CZ123456789','issuerCountryCode':'CZ'}],'bankDetails':[{'name':'Russian Bank Name','settlementLocalCurrency':'RUB','settlementForeignCurrency':'USD'}],'typeCode':'business'},'sellerDetails':{'postalAddress':{'postalCode':'14800','cityName':'Prague','countryCode':'CZ','provinceCode':'CZ','addressLine1':'V Parku 2308/10','addressLine2':'addres2','addressLine3':'addres3','countyName':'Central Bohemia','provinceName':'Central Bohemia','countryName':'Czech Republic'},'contactInformation':{'email':'that@before.de','phone':'+1123456789','mobilePhone':'+60112345678','companyName':'Company Name','fullName':'John Brew'},'registrationNumbers':[{'typeCode':'VAT','number':'CZ123456789','issuerCountryCode':'CZ'}],'bankDetails':[{'name':'Russian Bank Name','settlementLocalCurrency':'RUB','settlementForeignCurrency':'USD'}],'typeCode':'business'},'payerDetails':{'postalAddress':{'postalCode':'14800','cityName':'Prague','countryCode':'CZ','provinceCode':'CZ','addressLine1':'V Parku 2308/10','addressLine2':'addres2','addressLine3':'addres3','countyName':'Central Bohemia','provinceName':'Central Bohemia','countryName':'Czech Republic'},'contactInformation':{'email':'that@before.de','phone':'+1123456789','mobilePhone':'+60112345678','companyName':'Company Name','fullName':'John Brew'},'registrationNumbers':[{'typeCode':'VAT','number':'CZ123456789','issuerCountryCode':'CZ'}],'bankDetails':[{'name':'Russian Bank Name','settlementLocalCurrency':'RUB','settlementForeignCurrency':'USD'}],'typeCode':'business'},'ultimateConsigneeDetails':{'postalAddress':{'postalCode':'14800','cityName':'Prague','countryCode':'CZ','provinceCode':'CZ','addressLine1':'V Parku 2308/10','addressLine2':'addres2','addressLine3':'addres3','countyName':'Central Bohemia','provinceName':'Central Bohemia','countryName':'Czech Republic'},'contactInformation':{'email':'that@before.de','phone':'+1123456789','mobilePhone':'+60112345678','companyName':'Company Name','fullName':'John Brew'},'registrationNumbers':[{'typeCode':'VAT','number':'CZ123456789','issuerCountryCode':'CZ'}],'bankDetails':{'typeCode':'VAT','number':'CZ123456789','issuerCountryCode':'CZ'},'typeCode':'string'}},'content':{'packages':[{'typeCode':'2BP','weight':22.501,'dimensions':{'length':15.001,'width':15.001,'height':40.001},'customerReferences':[{'value':'Customer reference','typeCode':'CU'}],'identifiers':[{'typeCode':'shipmentId','value':'1234567890','dataIdentifier':'00'}],'description':'Piece content description','labelBarcodes':[{'position':'left','symbologyCode':'93','content':'string','textBelowBarcode':'text below left barcode'}],'labelText':[{'position':'left','caption':'text caption','value':'text value'}],'labelDescription':'bespoke label description'}],'isCustomsDeclarable':true,'declaredValue':150,'declaredValueCurrency':'CZK','exportDeclaration':{'lineItems':[{'number':1,'description':'line item description','price':150,'quantity':{'value':1,'unitOfMeasurement':'BOX'},'commodityCodes':[{'typeCode':'outbound','value':851713}],'exportReasonType':'permanent','manufacturerCountry':'CZ','weight':{'netValue':10,'grossValue':10},'isTaxesPaid':true,'additionalInformation':['string'],'customerReferences':[{'typeCode':'AFE','value':'custref123'}],'customsDocuments':[{'typeCode':'972','value':'custdoc456'}],'preCalculatedLineItemTotalValue':150}],'invoice':{'number':'12345-ABC','date':'2020-03-18','signatureName':'Brewer','signatureTitle':'Mr.','signatureImage':'Base64 encoded image','instructions':['string'],'customerDataTextEntries':['string'],'totalNetWeight':999999999999,'totalGrossWeight':999999999999,'customerReferences':[{'typeCode':'CU','value':'custref112'}],'termsOfPayment':'100 days','indicativeCustomsValues':{'importCustomsDutyValue':150.57,'importTaxesValue':49.43,'totalWithImportDutiesAndTaxes':[350.57]},'preCalculatedTotalValues':{'preCalculatedTotalGoodsValue':49.43,'preCalculatedTotalInvoiceValue':150.57}},'remarks':[{'value':'declaration remark'}],'additionalCharges':[{'value':10,'caption':'fee','typeCode':'freight'}],'destinationPortName':'port details','placeOfIncoterm':'port of departure or destination details','payerVATNumber':'12345ED','recipientReference':'recipient reference','exporter':{'id':'123','code':'EXPCZ'},'packageMarks':'marks','declarationNotes':[{'value':'up to three declaration notes'}],'exportReference':'export reference','exportReason':'export reason','exportReasonType':'permanent','licenses':[{'typeCode':'export','value':'license'}],'shipmentType':'personal','customsDocuments':[{'typeCode':'972','value':'custdoc445'}]},'description':'shipment description','USFilingTypeValue':'12345','incoterm':'DAP','unitOfMeasurement':'metric'},'documentImages':[{'typeCode':'INV','imageFormat':'PDF','content':'base64 encoded image'}],'onDemandDelivery':{'deliveryOption':'servicepoint','location':'front door','specialInstructions':'ringe twice','gateCode':'1234','whereToLeave':'concierge','neighbourName':'Mr.Dan','neighbourHouseNumber':'777','authorizerName':'Newman','servicePointId':'SPL123','requestedDeliveryDate':'2020-04-20'},'requestOndemandDeliveryURL':false,'shipmentNotification':[{'typeCode':'email','receiverId':'receiver@email.com','languageCode':'eng','languageCountryCode':'UK','bespokeMessage':'message to be included in the notification'}],'prepaidCharges':[{'typeCode':'freight','currency':'CZK','value':200,'method':'cash'}],'getTransliteratedResponse':false,'estimatedDeliveryDate':{'isRequested':false,'typeCode':'QDDC'},'getAdditionalInformation':[{'typeCode':'pickupDetails','isRequested':true}],'parentShipment':{'productCode':'s','packagesCount':1}}",
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic REPLACE_BASIC_AUTH",
                "Message-Reference: SOME_STRING_VALUE",
                "Message-Reference-Date: SOME_STRING_VALUE",
                "Plugin-Name: SOME_STRING_VALUE",
                "Plugin-Version: SOME_STRING_VALUE",
                "Shipping-System-Platform-Name: SOME_STRING_VALUE",
                "Shipping-System-Platform-Version: SOME_STRING_VALUE",
                "Webstore-Platform-Name: SOME_STRING_VALUE",
                "Webstore-Platform-Version: SOME_STRING_VALUE",
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return  $response;
        }
    }

    public function calculatePickup(Shipment $shipment, ShipmentItem $shipmentItem, Request $request)
    {
        $origin = ShipmentAddress::where(['shipment_id' => $this->shipment->id,'type' => 'origin'])->first();
        $destination = ShipmentAddress::where(['shipment_id' => $this->shipment->id,'type' => 'destination'])->first();
        $product_code = $this->productCode($origin, $destination, $shipment);
        $type = $this->isInternational($origin, $destination);
        $shipment_date = Carbon::create($request->shipment_date)->timezone('GMT+1');
        $shipment_date = str_replace(' ', 'T', $shipment_date->toDateTimeString()) . " GMT+01:00";

        $pickupPayload = [
            "plannedPickupDateAndTime" => $shipment_date,
            "closeTime" => "18:00",
            "location" => "reception",
            "locationType" => "residence",
            "accounts" => [
                [
                    "number" => $this->account_number,
                    "typeCode" => "shipper"
                ]
            ],
            /*"specialInstructions" => [
                [
                    "value" => "Optional special instructions",
                    "typeCode" => "TBD"
                ]
            ],*/
            "customerDetails" => [
                "shipperDetails" => [
                    'postalAddress' => [
                        "postalCode" => $origin['postcode'] ?? '',
                        "cityName" => getCity('id', $origin['city'])->name,
                        "countryCode" => getCountry('id', $origin['country'])->iso2,
                        "addressLine1" => substr($origin['address_1'], 0, 45),
                        "addressLine2" => !empty($origin['address_2']) ? $origin['address_2'] : $origin['landmark'],
                        "addressLine3" => $origin['landmark'],
                        "countyName" => getState('id', $origin['state'])->name,
                    ],
                    'contactInformation' => [
                        "email" => $origin['contact_email'] ?? 'muhjamie@gmail.com',
                        "phone" => $origin['contact_phone'],
                        "companyName" => $origin['company_name'] ?? $origin['contact_name'],
                        "fullName" => $origin['contact_name']
                    ],
                ],
                "receiverDetails" => [
                    'postalAddress' => [
                        "postalCode" => $destination['postcode'] ?? '',
                        "cityName" => getCity('id', $destination['city'])->name,
                        "countryCode" => getCountry('id', $destination['country'])->iso2,
                        "addressLine1" => substr($destination['address_1'], 0, 45),
                        "addressLine2" => !empty($destination['address_2']) ? $destination['address_2'] : $destination['landmark'],
                        "addressLine3" => $destination['landmark'],
                        "countyName" => getState('id', $destination['state'])->name,
                    ],
                    'contactInformation' => [
                        "email" => $destination['contact_email'] ?? 'muhjamie@gmail.com',
                        "phone" => $destination['contact_phone'],
                        "companyName" => $destination['company_name'] ?? $destination['contact_name'],
                        "fullName" => $destination['contact_name']
                    ],
                ]
            ],
            "shipmentDetails" => [
                [
                    "productCode" => "P",
                    "isCustomsDeclarable" => true,
                    "unitOfMeasurement" => "metric",
                    "declaredValueCurrency" => "NGN",
                    "declaredValue" => (int) $shipmentItem->value,
                    "packages" => [
                        [
                            'weight' => $shipmentItem->weight,
                            'dimensions' => [
                                'length' => $shipmentItem->length,
                                'width' => $shipmentItem->width,
                                'height' => $shipmentItem->height,
                            ],
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendRequest($this->base_url . '/pickups', $pickupPayload);
    }

    private function sendRequest($url, $payload)
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_USERPWD => $this->username . ':' . $this->password,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => array(
                    'Message-Reference: ' . Str::uuid(),
                    'Content-Type: application/json',
                    'Accept: application/json',
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response, true);
            if (!isset($result['dispatchConfirmationNumbers'])) return false;
            return $result;
        } catch (\Throwable $e) {
            $response = $e->getMessage();
            return false;
        }
    }
}
