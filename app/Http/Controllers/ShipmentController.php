<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreatePackageInformationRequest;
use App\Http\Requests\CreateShipmentDestinationRequest;
use App\Http\Requests\CreateShipmentOriginRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Http\Requests\TrackShipmentRequest;
use App\Mail\OrderConfirmation;
use App\Models\Address;
use App\Models\AllowedShipmentCountry;
use App\Models\AramexShipmentLog;
use App\Models\City;
use App\Models\Country;
use App\Models\DhlRateLog;
use App\Models\InsuranceOption;
use App\Models\ItemCategory;
use App\Models\Shipment;
use App\Models\ShipmentAddress;
use App\Models\ShipmentItem;
use App\Models\ShippingRateLog;
use App\Models\State;
use App\Models\TrackingLog;
use App\Services\ShipmentServices;
use App\Services\UpsServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ShipmentController extends Controller
{
    public function index()
    {
        //Mail::to(auth()->user()->email)->send(new OrderConfirmation(''));
        $filter = request();
        $log = [];
        $shipments = Shipment::with('shipping_rate_log')->where('user_id', auth()->user()->id)
        ->where('status', '!=', 'failed')->where(function ($query) use ($filter) {
            $query->when($filter->filled('status'), function ($query) use ($filter) {
                return $filter->get('status') !== 'all'
                    ? $query->where('status', $filter->get('status'))
                    : $query;
            });
        })->orderBy('id', 'desc')->paginate(10);
        $shipmentsCount = Shipment::where('user_id', auth()->user()->id)->count();
        foreach ($shipments as $shipment) {
            $origin = ShipmentAddress::where([
                'shipment_id'=>$shipment->id,
                'type'=>'origin'
            ])->first();

            $destination = ShipmentAddress::where([
                'shipment_id'=>$shipment->id,
                'type'=>'destination'
            ])->first();

            $log[] = [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'origin' => [
                    'name' => $origin->contact_name,
                    'phone' => $origin->contact_phone,
                    'email' => $origin->contact_email,
                    'address_1' => $origin->address_1,
                    'city' => getCity('id' , $origin->city_id)->name,
                    'country' => getCountry('id' , $origin->country_id)->name,
                ],
                'destination' => [
                    'name' => $destination->contact_name,
                    'phone' => $destination->contact_phone,
                    'email' => $destination->contact_email,
                    'address_1' => $destination->address_1,
                    'city' => getCity('id' , $destination->city_id)->name,
                    'country' => getCountry('id' , $destination->country_id)->name,
                ],
                'status' => $shipment->status
            ];
        }

        return Inertia::render('Shipments/Index', compact(
            'log', 'shipments', 'shipmentsCount'
        ));
    }

    public function calculatePickup(Request $request, ShipmentServices $services)
    {
        return $services->calculatePickup($request);
    }

    public function create()
    {
        $addresses = Address::where('user_id', auth()->user()->id)
            ->with('address_contacts', 'country', 'city')->get();
        $all_countries = Country::all();
        $countries = [];
        foreach ($all_countries as $country) {
            $allowed = AllowedShipmentCountry::where('country_id', $country->id)->count();
            $countries[] = [
                'id' => $country->id,
                'name' => $country->name,
                'can_ship_from' => $allowed > 0
            ];
        }

        $categories = ItemCategory::all();
        return Inertia::render('Shipments/Create', compact('addresses', 'countries', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function filterShipment(Request $request): \Illuminate\Http\JsonResponse
    {
        $log = [];
        $shipments = Shipment::where('user_id', auth()->user()->id)->where('has_rate', 1)
            ->where(function ($query) use ($request) {
                $query->when($request->filled('status'), function ($query) use ($request) {
                    return $request->get('status') !== 'all'
                        ? $query->where('status', $request->get('status'))
                        : $query;
                });
            })->with('shipment_rate')->orderBy('id', 'desc')->paginate(10);
        $shipmentsCount = Shipment::where('user_id', auth()->user()->id)->count();
        foreach ($shipments as $shipment) {
            $origin = ShipmentAddress::where([
                'shipment_id'=>$shipment->id,
                'type'=>'origin'
            ])->first();

            $destination = ShipmentAddress::where([
                'shipment_id'=>$shipment->id,
                'type'=>'destination'
            ])->first();
            $log[] = [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'origin' => [
                    'name' => $origin['contact_name'],
                    'phone' => $origin['contact_phone'],
                    'email' => $origin['contact_email'],
                    'address_1' => $origin['address_1'],
                    'city' => getCity('id' , $origin['city_id'])->name,
                    'country' => getCountry('id' , $origin['country_id'])->name,
                ],
                'destination' => [
                    'name' => $destination['contact_name'],
                    'phone' => $destination['contact_phone'],
                    'email' => $destination['contact_email'],
                    'address_1' => $destination['address_1'],
                    'city' => getCity('id' , $destination['city_id'])->name,
                    'country' => getCountry('id' , $destination['country_id'])->name,
                ],
                'status' => $shipment->status
            ];
        }

        return response()->json($log);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shipment = Shipment::whereId($id)->with('shipment_items', 'country', 'city', 'state')->first();
        $item_category = ItemCategory::find($shipment->shipment_items[0]->item_category_id);
        $origin = ShipmentAddress::where(['shipment_id'=>$shipment->id, 'type'=>'origin'])->first();
        $destination = ShipmentAddress::where(['shipment_id'=>$shipment->id, 'type'=>'destination'])->first();

        $origin_location = [
            'country' => getCountry('id', $origin->country_id)->name,
            'state' => getState('id', $origin->state_id)->name,
            'city' => getCity('id', $origin->city_id)->name,
        ];

        $destination_location = [
            'country' => getCountry('id', $destination->country_id)->name,
            'state' => getState('id', $destination->state_id)->name,
            'city' => getCity('id', $destination->city_id)->name,
        ];

        $insurance_options = InsuranceOption::all();
        $shipping_rate_log = ShippingRateLog::where('id', $shipment->shipping_rate_log_id)->with('courier_api_provider')->first();
        $tracking_log = TrackingLog::where('shipment_id', $shipment->id)->get();
        return Inertia::render('Shipments/Details', compact('shipment','tracking_log','item_category','origin', 'destination','insurance_options','shipping_rate_log', 'origin_location', 'destination_location'));
    }



    public function calculateShipment(CreateShipmentRequest $request, ShipmentServices $shipmentServices)
    {
        return $shipmentServices->calculateShipmentCost($request);
    }

    public function recalculateShipment(CreateShipmentRequest $request, ShipmentServices $shipmentServices)
    {
        return $shipmentServices->recalculateShipmentCost($request);
    }

    public function initialize(Request $request)
    {
        //dd($request);
    }

    public function checkout($id) {
        $shipment = Shipment::whereId($id)->with('shipment_items', 'country', 'city', 'state')->first();
        if ($shipment->status == 'processing') return redirect(route('shipment.details', $id));
        $shipping_rate_log = ShippingRateLog::where(['shipment_id' => $id, 'user_id' => auth()->user()->id])->with('courier_api_provider')->get();
        $shipment_item = ShipmentItem::find($shipment->shipment_items[0]->id);
        $item_category = ItemCategory::find($shipment_item->item_category_id);
        $origin = ShipmentAddress::where(['shipment_id' => $shipment->id, 'type' => 'origin'])->first();
        $destination = ShipmentAddress::where(['shipment_id' => $shipment->id, 'type' => 'destination'])->first();
        $origin_location = [
            'country' => getCountry('id', $origin->country_id)->name,
            'state' => getState('id', $origin->state_id)->name,
            'city' => getCity('id', $origin->city_id)->name,
        ];

        $destination_location = [
            'country' => getCountry('id', $destination->country_id)->name,
            'state' => getState('id', $destination->state_id)->name,
            'city' => getCity('id', $destination->city_id)->name,
        ];
        $insurance_options = InsuranceOption::all();
        $dhl_rate_log = DhlRateLog::where('shipment_id', $id)->get();
        $countries = Country::all();
        $origin_states = State::where('country_id', $origin->country)->get();
        $origin_cities = City::where('state_id', $origin->state)->get();
        $destination_states = State::where('country_id', $destination->country)->get();
        $destination_cities = City::where('state_id', $destination->state)->get();
        $categories = ItemCategory::all();
        return Inertia::render('Shipments/Checkout', compact('countries', 'categories','origin_states', 'origin_cities', 'destination_states', 'destination_cities','item_category','shipment', 'dhl_rate_log','origin', 'destination','insurance_options','shipping_rate_log', 'origin_location', 'destination_location'));
    }

    /**
     * @throws ValidationException
     */
    public function bookShipment(BookShipmentRequest $request, ShipmentServices $services)
    {
        return $services->bookShipment($request);
    }

    public function trackShipment(TrackShipmentRequest $request, ShipmentServices $services)
    {
        return $services->trackShipment($request);//
    }

    public function trackingDetails($shipment_id)
    {
        $tracking_log = TrackingLog::where(['shipment_id' => $shipment_id])->get();
        return Inertia::render('Shipments/TrackingDetails', compact('tracking_log'));
    }

    public function origin($shipment_id = null): \Inertia\Response
    {
        $countries = Country::all();
        $states = $cities = $origin_address = [];
        $addresses = Address::where('user_id', auth()->user()->id)
            ->with('address_contacts', 'country', 'city')->get();

        $check_origin = ShipmentAddress::where([
            'shipment_id' => $shipment_id,
            'type' => 'origin',
        ])->first();

        if ($check_origin) {
            $origin_address = ShipmentAddress::where([
                'shipment_id' => $shipment_id,
                'type' => 'origin',
            ])->first();
            $states = State::where('country_id', $origin_address->country_id)->get();
            $cities = City::where('state_id', $origin_address->state_id)->get();
        }

        return Inertia::render('Shipments/Origin', compact('countries', 'states', 'cities', 'origin_address', 'addresses'));
    }

    public function storeOrigin(CreateShipmentOriginRequest $request, ShipmentServices $services, $shipment_id = null): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $shipment = $request->shipment_id > 0 ? Shipment::find($request->shipment_id) : new Shipment;
        $shipment->user_id = auth()->user()->id;
        $shipment->status = 'pending';
        $shipment->reference = Str::uuid();
        $shipment->created_at = now();
        $shipment->save();

        ShipmentAddress::updateOrCreate(['shipment_id' => $shipment->id, 'type' => 'origin'],[
            'contact_name' => $request->contact_name,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'business_name' => $request->business_name,
            'address_1' => $request->address_1,
            'landmark' => $request->landmark,
            'address_2' => $request->address_2,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'postcode' => $request->postcode,
        ]);

        $services->validateAddress($request, $shipment->id);
        return redirect(route('shipment.destination', $shipment->id));
    }

    public function destination($shipment_id): \Inertia\Response
    {
        $addresses = Address::where('user_id', auth()->user()->id)
            ->with('address_contacts', 'country', 'city')->get();
        $origin_country = ShipmentAddress::where([
            'shipment_id' => $shipment_id,
            'type' => 'origin',
        ])->first();
        $allowed = AllowedShipmentCountry::where('country_id', $origin_country->country_id)->first();
        $destinations = explode(',', $allowed->allowed_destinations);
        $countries = [];
        foreach ($destinations as $destination) {
            $countries[] = [
                'id' => $destination,
                'name' => getCountry('id', $destination)->name
            ];
        }

        $states = $cities = $destination_address = [];
        $check_destination = ShipmentAddress::where([
            'shipment_id' => $shipment_id,
            'type' => 'destination',
        ])->first();

        if ($check_destination) {
            $destination_address = ShipmentAddress::where([
                'shipment_id' => $shipment_id,
                'type' => 'destination',
            ])->first();
            $states = State::where('country_id', $destination_address->country_id)->get();
            $cities = City::where('state_id', $destination_address->state_id)->get();
        }


        return Inertia::render('Shipments/Destination', compact('shipment_id', 'countries', 'destination_address', 'states', 'cities', 'addresses'));
    }

    public function storeDestination(CreateShipmentDestinationRequest $request, ShipmentServices $services, $id = null): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $shipment = Shipment::find($request->shipment_id);
        ShipmentAddress::updateOrCreate(['shipment_id' => $shipment->id, 'type' => 'destination'],[
            'contact_name' => $request->contact_name,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'business_name' => $request->business_name,
            'address_1' => $request->address_1,
            'landmark' => $request->landmark,
            'address_2' => $request->address_2,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'postcode' => $request->postcode,
        ]);

        $services->validateAddress($request, $shipment->id, 'delivery');
        return redirect(route('shipment.package-information', $shipment->id));
    }

    public function packageInformation($shipment_id): \Inertia\Response
    {
        $categories = ItemCategory::all();
        $item = ShipmentItem::where('shipment_id', $shipment_id)->first() ?? [];
        return Inertia::render('Shipments/PackageInformation', compact('categories', 'shipment_id', 'item'));
    }

    public function storePackageInformation(CreatePackageInformationRequest $request, ShipmentServices $services, $id = null): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $shipment = Shipment::find($request->shipment_id);
        if ($shipment->status == 'processing') return redirect(route('shipment.details', $id));
        ShipmentItem::updateOrCreate(['shipment_id' => $shipment->id],[
            'item_category_id' => $request->category,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'weight' => $request->weight,
            'height' => $request->height,
            'length' => $request->length,
            'width' => $request->width,
            'value' => $request->value,
        ]);

        return $services->calculateShipmentCost($shipment->id);
    }
}
