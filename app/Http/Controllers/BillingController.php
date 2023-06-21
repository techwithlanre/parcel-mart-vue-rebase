<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingController extends Controller
{
    public function cards()
    {
        $cards = Card::where('user_id', auth()->user()->id)->paginate(1);
        return Inertia::render('Billing/Cards', compact('cards'));
    }

    public function createCard(Request $request)
    {
        $payload = [
            'user_id'=>auth()->user()->id,
            'pan'=>$request->pan,
            'expiry'=>$request->expiry,
            'cvv'=>$request->cvv,
        ];

        Card::create($payload);
    }
}
