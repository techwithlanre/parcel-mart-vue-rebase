<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookShipmentRequest;
use App\Http\Requests\CreatePackageInformationRequest;
use App\Http\Requests\CreateShipmentDestinationRequest;
use App\Http\Requests\CreateShipmentOriginRequest;
use App\Models\Address;
use App\Models\AddressContact;
use App\Models\AllowedShipmentCountry;
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
use App\Models\User;
use App\Services\ShipmentServices;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = request();
        $log = [];
        $shipments = Shipment::with('shipping_rate_log')->where('user_id', auth()->user()->id)->where('has_rate', 1)
            ->where('status', '!=', 'failed')->where(function ($query) use ($filter) {
                $query->when($filter->filled('status'), function ($query) use ($filter) {
                    return $filter->get('status') !== 'all'
                        ? $query->where('status', $filter->get('status'))
                        : $query;
                });
            })->orderBy('id', 'desc')->paginate(10);

        foreach ($shipments as $shipment) {
            $origin = ShipmentAddress::where(['shipment_id'=>$shipment->id, 'type' => 'origin'])->first();
            $destination = ShipmentAddress::where(['shipment_id'=>$shipment->id, 'type' => 'destination'])->first();
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
                'status' => $shipment->status,
                'rate' => $shipment->shipping_rate_log
            ];
        }
        return Inertia::render('Admin/Shipments/Index', compact(
            'log', 'shipments'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function filterShipment(Request $request): \Illuminate\Http\JsonResponse
    {
        $log = [];

        $shipments = Shipment::where(function ($query) use ($request) {
                $query->when($request->filled('number'), function ($query) use ($request) {
                    $query->where('number', 'LIKE', '%'. $request->get('number'). '%');
                });
            })->where(function ($query) use ($request) {
                $query->when($request->filled('status'), function ($query) use ($request) {
                    if ($request->get('status') !== 'all')
                        $query->where('status', $request->get('status'));
                });
            })->with('user', 'origin', 'destination')->orderBy('id', 'desc')->paginate(10);
//        foreach ($shipments as $shipment) {
//            $origin = ShipmentAddress::where([
//                'shipment_id'=>$shipment->id,
//                'type'=>'origin'
//            ])->first();
//
//            $destination = ShipmentAddress::where([
//                'shipment_id'=>$shipment->id,
//                'type'=>'destination'
//            ])->first();
//            $log[] = [
//                'id' => $shipment->id,
//                'number' => $shipment?->number,
//                'origin' => [
//                    'name' => $origin?->contact_name,
//                    'phone' => $origin?->contact_phone,
//                    'email' => $origin?->contact_email,
//                    'address_1' => $origin?->address_1,
//                    'city' => getCity('id' , $origin?->city_id)?->name,
//                    'country' => getCountry('id' , $origin?->country_id)?->name,
//                ],
//                'destination' => [
//                    'name' => $destination?->contact_name,
//                    'phone' => $destination?->contact_phone,
//                    'email' => $destination?->contact_email,
//                    'address_1' => $destination?->address_1,
//                    'city' => getCity('id' , $destination?->city_id)?->name,
//                    'country' => getCountry('id' , $destination?->country_id)?->name,
//                ],
//                'status' => $shipment->status,
//                'rate' => $shipment?->shipping_rate_log,
//                'user' => User::find($shipment->user_id),
//            ];
//        }

        $response = [
            'data' => $log,
            'links' => $shipments->links()
        ];

        return response()->json($shipments);
    }

    public function export(Request $request): \Illuminate\Http\JsonResponse
    {
        $log = [];
        $shipments = Shipment::where('user_id', auth()->user()->id)->where('has_rate', 1)
            ->where(function ($query) use ($request) {
                $query->when($request->filled('number'), function ($query) use ($request) {
                    return $query->where('number', 'LIKE', '%'. $request->get('number'). '%');
                });
            })->where(function ($query) use ($request) {
                $query->when($request->filled('status'), function ($query) use ($request) {
                    return $request->get('status') !== 'all'
                        ? $query->where('status', $request->get('status'))
                        : $query;
                });
            })->with('shipment_rate')->orderBy('id', 'desc')->paginate(10);

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
                'status' => $shipment->status,
                'rate' => $shipment->shipping_rate_log,
            ];
        }


        return response()->json($log);
    }

    public function origin($shipment_id = null): \Inertia\Response
    {
        //if (!\request()->user_id) to_route('da');
        $user_id = ($shipment_id == null)
            ? request()->user
            : Shipment::find($shipment_id)->user_id;
        $user = User::find($user_id);
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

        return Inertia::render('Admin/Shipments/Origin', compact('countries', 'states', 'cities', 'origin_address', 'addresses', 'user'));
    }

    public function storeOrigin(CreateShipmentOriginRequest $request, ShipmentServices $services, $shipment_id = null): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $shipment = $request->shipment_id > 0 ? Shipment::find($request->shipment_id) : new Shipment;
        $shipment->user_id = $request->user_id;
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

        if ($request->save_address) {
            $address = Address::updateOrCreate([
                'address_1' => $request->address_1,
                'city_id' => $request->city_id,
                'shipment_id'=>$shipment->id,],
                [
                    'user_id'=>$request->user_id,
                    'address_2' => $request->address_2,
                    'country_id' => $request->country_id,
                    'state_id' => $request->state_id,
                    'landmark' => $request->landmark,
                    'postcode' => $request->postcode
                ]);

            AddressContact::updateOrCreate(['address_id' => $address->id],[
                'contact_name' => $request->contact_name,
                'business_name' => $request->business_name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'is_default' => $address->address_contacts()->count() > 0 ? 0 : 1,
            ]);
        }
        return redirect(route('admin.shipment.destination', $shipment->id));
    }

    public function destination($shipment_id): \Inertia\Response
    {
        $shipment = Shipment::find($shipment_id);
        $addresses = Address::where('user_id', $shipment->user_id)
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
        $user = User::find($shipment->user_id);
        return Inertia::render('Admin/Shipments/Destination', compact('shipment_id', 'user','countries', 'destination_address', 'states', 'cities', 'addresses'));
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

        if ($request->save_address) {
            $address = Address::updateOrCreate(['address_1' => $request->address_1, 'city_id' => $request->city_id,],[
                'shipment_id'=>$shipment->id,
                'user_id'=>$shipment->user_id,
                'address_2' => $request->address_2,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'landmark' => $request->landmark,
                'postcode' => $request->postcode,
            ]);

            AddressContact::updateOrCreate(['address_id' => $address->id],[
                'contact_name' => $request->contact_name,
                'business_name' => $request->business_name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'is_default' => $address->address_contacts()->count() > 0 ? 0 : 1,
            ]);
        }

        return redirect(route('admin.shipment.package-information', $shipment->id));
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
        if ($shipment->status == 'processing') return redirect(route('admin.shipment.details', $id));
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

    public function checkout($id) {
        $shipment = Shipment::whereId($id)->with('shipment_items', 'country', 'city', 'state')->first();
        if ($shipment->status == 'processing') {
            return redirect(route('admin.shipment.details', $id));
        }
        $shipping_rate_log = ShippingRateLog::where(['shipment_id' => $id, 'user_id' => $shipment->user_id])->with('courier_api_provider')->get();
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
        $user = User::find($shipment->user_id);
        return Inertia::render('Admin/Shipments/Checkout', compact('user','countries', 'categories','origin_states', 'origin_cities', 'destination_states', 'destination_cities','item_category','shipment', 'dhl_rate_log','origin', 'destination','insurance_options','shipping_rate_log', 'origin_location', 'destination_location'));
    }

    public function bookShipment(BookShipmentRequest $request, ShipmentServices $services)
    {
        return $services->bookShipment($request);
    }

    public function show(string $id)
    {
        $shipment = Shipment::whereId($id)->with('shipment_items', 'country', 'city', 'state')->first();
        $user = User::find($shipment->user_id);
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
        return Inertia::render('Admin/Shipments/Details', compact('shipment','user','tracking_log','item_category','origin', 'destination','insurance_options','shipping_rate_log', 'origin_location', 'destination_location'));
    }
}
