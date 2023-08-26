<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookShipmentRequest;
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
use App\Models\ShipmentItem;
use App\Models\ShippingRateLog;
use App\Models\State;
use App\Models\TrackingLog;
use App\Services\ShipmentServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ShipmentController extends Controller
{
    public function index()
    {
        //Mail::to(auth()->user()->email)->send(new OrderConfirmation(''));
        $filter = request();
        $log = [];
        $shipments = Shipment::where('user_id', auth()->user()->id)->where('has_rate', 1)
        ->where('status', '!=', 'failed')->where(function ($query) use ($filter) {
            $query->when($filter->filled('status'), function ($query) use ($filter) {
                return $filter->get('status') !== 'all'
                    ? $query->where('status', $filter->get('status'))
                    : $query;
            });
        })->with('shipment_rate')->orderBy('id', 'desc')->paginate(10);
        $shipmentsCount = Shipment::where('user_id', auth()->user()->id)->count();
        foreach ($shipments as $shipment) {
            $origin = json_decode($shipment->origin_address, true);
            $destination = json_decode($shipment->destination_address, true);
            $log[] = [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'origin' => [
                    'name' => $origin['contact_name'],
                    'phone' => $origin['contact_phone'],
                    'email' => $origin['contact_email'],
                    'address_1' => $origin['address_1'],
                    'city' => getCity('id' , $origin['city'])->name,
                    'country' => getCountry('id' , $origin['country'])->name,
                ],
                'destination' => [
                    'name' => $destination['contact_name'],
                    'phone' => $destination['contact_phone'],
                    'email' => $destination['contact_email'],
                    'address_1' => $destination['address_1'],
                    'city' => getCity('id' , $destination['city'])->name,
                    'country' => getCountry('id' , $destination['country'])->name,
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
            $origin = json_decode($shipment->origin_address, true);
            $destination = json_decode($shipment->destination_address, true);
            $log[] = [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'origin' => [
                    'name' => $origin['contact_name'],
                    'phone' => $origin['contact_phone'],
                    'email' => $origin['contact_email'],
                    'address_1' => $origin['address_1'],
                    'city' => getCity('id' , $origin['city'])->name,
                    'country' => getCountry('id' , $origin['country'])->name,
                ],
                'destination' => [
                    'name' => $destination['contact_name'],
                    'phone' => $destination['contact_phone'],
                    'email' => $destination['contact_email'],
                    'address_1' => $destination['address_1'],
                    'city' => getCity('id' , $destination['city'])->name,
                    'country' => getCountry('id' , $destination['country'])->name,
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
        $origin = json_decode($shipment->origin_address);
        $destination = json_decode($shipment->destination_address);
        $origin_location = [
            'country' => getCountry('id', $origin->country)->name,
            'state' => getState('id', $origin->state)->name,
            'city' => getCity('id', $origin->city)->name,
        ];

        $destination_location = [
            'country' => getCountry('id', $destination->country)->name,
            'state' => getState('id', $destination->state)->name,
            'city' => getCity('id', $destination->city)->name,
        ];

        $insurance_options = InsuranceOption::all();
        $shipping_rate_log = ShippingRateLog::where('id', $shipment->shipping_rate_log_id)->with('courier_api_provider')->first();
        $tracking_log = [];
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
        dd($request);
    }

    public function checkout($id) {
        $shipment = Shipment::whereId($id)->with('shipment_items', 'country', 'city', 'state')->first();
        if ($shipment->status == 'processing') return redirect(route('shipment.details', $id));
        $shipping_rate_log = ShippingRateLog::where('shipment_id', $id)->with('courier_api_provider')->get();
        $shipment_item = ShipmentItem::find($shipment->id);
        $item_category = ItemCategory::find($shipment_item->item_category_id);
        $origin = json_decode($shipment->origin_address);
        $destination = json_decode($shipment->destination_address);
        $origin_location = [
            'country' => getCountry('id', $origin->country)->name,
            'state' => getState('id', $origin->state)->name,
            'city' => getCity('id', $origin->city)->name,
        ];

        $destination_location = [
            'country' => getCountry('id', $destination->country)->name,
            'state' => getState('id', $destination->state)->name,
            'city' => getCity('id', $destination->city)->name,
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
}
