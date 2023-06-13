<?php

namespace App\Http\Controllers;

use App\Models\ShippingDescriptionSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShippingSettingController extends Controller
{
    public function description()
    {
        $descriptions = ShippingDescriptionSetting::where('user_id', auth()->user()->id)->paginate(5);
        return Inertia::render('Setting/Shipping/Description', compact('descriptions'));
    }

    public function createDescription(Request $request)
    {
        $data = [
            'user_id' => auth()->user()->id,
            'description' => $request->description
        ];
        $check = ShippingDescriptionSetting::where('user_id', $data['user_id'])->count();
        $data['is_default'] = $check > 0 ? 0 : 1;
        ShippingDescriptionSetting::create($data);
    }

    public function measurement()
    {
        $descriptions = ShippingDescriptionSetting::where('user_id', auth()->user()->id)->paginate(5);
        return Inertia::render('Setting/Shipping/Measurement', compact('descriptions'));
    }

}
