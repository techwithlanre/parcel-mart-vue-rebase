<?php

namespace App\Services;

use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Models\DhlShipmentLog;
use App\Models\InsuranceOption;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShippingRateLog;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use League\Flysystem\Config;
use GuzzleHttp\Psr7;
use Illuminate\Support\Str;

class DHLServices
{
    protected $request;
    protected String $env;
    protected String $base_url;
    protected String $account_number;
    protected String $username;
    protected String $password;
    protected array $calculateRatePayload;
    protected array $bookShipmentPayload;
    public function __construct(CreateShipmentRequest | BookShipmentRequest $request)
    {
        $this->request = $request;
        $this->initialize();
    }

    private function initialize(): void
    {
        $this->env = \config('dhl.ENV');
        $this->base_url = \config('dhl.'.$this->env.'.baseUrl');
        $this->account_number = \config('dhl.'.$this->env.'.accountNumber');
        $this->username = \config('dhl.'.$this->env.'.username');
        $this->password = \config('dhl.'.$this->env.'.password');
    }

    public function calculateRate()
    {
        $this->calculateRatePayload();
        return $this->call('rates');
    }

    private function calculateRatePayload(): void
    {
        $this->calculateRatePayload = [
            'accountNumber' => $this->account_number,
            'originCountryCode'=> getCountry('id', $this->request->origin['country'])->iso2,
            'originCityName' => getCity('id', $this->request->origin['city'])->name,
            'destinationCountryCode' => getCountry('id', $this->request->destination['country'])->iso2,
            'destinationCityName' => getCity('id', $this->request->destination['city'])->name,
            'weight' => $this->request->shipment['weight'],
            'length' => $this->request->shipment['length'],
            'width' => $this->request->shipment['width'],
            'height' => $this->request->shipment['height'],
            'plannedShippingDate' => '2024-02-26',
            'isCustomsDeclarable' => 'false', //based on country
            'unitOfMeasurement' => 'metric',
            'nextBusinessDay' => 'false',
            'strictValidation' => 'false',
            'getAllValueAddedServices' => 'false',
            'requestEstimatedDeliveryDate' => 'true',
            'estimatedDeliveryDateType' => 'QDDF'
        ];
    }

    public function bookShipment(Shipment $shipment, ShipmentItem $shipmentItem, InsuranceOption $insuranceOption, ShippingRateLog $shippingRateLog)
    {
        $this->bookShipmentPayload($shipment, $shipmentItem, $insuranceOption, $shippingRateLog);
        $result = $this->sendBookShipmentRequest();
        $dhl_shipment_log = DhlShipmentLog::create([
            'shipment_id' => $shipment->id,
            'shipment_rate_log_id' => $shippingRateLog->id,
            'shipment_tracking_number' => $result['shipmentTrackingNumber'],
            'tracking_url' => $result['trackingUrl'],
            'document_content' => json_encode($result['documents']),
            'package_details' => json_encode($result['packages'])
        ]);

        dd($dhl_shipment_log);
        //Mail::to(auth()->user()->email)->send(new OrderConfirmation($shipment_data));
        //return true;
    }



    private function bookShipmentPayload(Shipment $shipment, ShipmentItem $shipmentItem, InsuranceOption $insuranceOption, ShippingRateLog $shippingRateLog)
    {
        $origin = json_decode($shipment->origin_address, true);
        $destination = json_decode($shipment->origin_address, true);
        //$product_code = (getCountry('id', $origin['country'])->iso2 == getCountry('id', $destination['country'])->iso2) ? 'DOM' : 'EXP';




        $today = Carbon::now();
        $tomorrow = $today->addDay();

        $this->bookShipmentPayload = [
            "plannedShippingDateAndTime" => "2023-07-14T17:10:09 GMT+01:00",
            "productCode" => "N",
            "pickup" => [
                "isRequested" => false
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
                    "number" => $this->account_number,
                    "typeCode" => "shipper"
                ]
            ],
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
                    "typeCode" => "business"
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
                    "typeCode" => "business"
                ]
            ],
            "content" => [
                "packages" => [
                    [
                        'weight' => $shipmentItem->weight,
                        'dimensions' => [
                            'length' => $shipmentItem->length,
                            'width' => $shipmentItem->width,
                            'height' => $shipmentItem->height,
                        ],
                        'description' => $shipment->description ?? 'Package Description'
                    ]
                ],
                "isCustomsDeclarable" => false,
                "description" => "Content Description 70characters",
                "incoterm" => "DAP",
                "unitOfMeasurement" => "metric"
            ]
        ];
    }

    private function sendBookShipmentRequest()
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://express.api.dhl.com/mydhlapi/test/shipments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_USERPWD => $this->username . ':' . $this->password,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($this->bookShipmentPayload),
                CURLOPT_HTTPHEADER => array(
                    'Message-Reference: ' . Str::uuid(),
                    'Content-Type: application/json',
                    'Accept: application/json',
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response, true);
            if (!isset($result['shipmentTrackingNumber'])) return false;
            return $result;
        } catch (\Exception $e) {
            $response = $e->getMessage();
            return false;
        }
    }



    private function call(String $path, $method = 'get'): bool|string
    {
        try {
            $client = new Client([
                'auth' => [$this->username, $this->password]
            ]);

            $response = $client->$method($this->base_url.'/'.$path, ['query' => $this->calculateRatePayload]);
            if ($response->getStatusCode() == 200)  return $response->getBody()->getContents();
            return false;
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            return false;
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
}
