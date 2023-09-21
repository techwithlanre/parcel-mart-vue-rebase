<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQuoteRequest;
use App\Http\Requests\SetQuotePriceRequest;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class QuoteController extends Controller
{
    public function requestQuote(CreateQuoteRequest $request)
    {
        $data = [
            'user_id' => auth()->user()->id ?? null,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'origin_country_id' => $request->country_from,
            'origin_state_id' => $request->state_from,
            'origin_city_id' => $request->city_from,
            'destination_country_id' => $request->country_to,
            'destination_state_id' => $request->state_to,
            'destination_city_id' => $request->city_to,
            'quantity' => $request->quantity,
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'commercial_invoice' => '',
            'parking_list' => '',
        ];

        $quote = Quote::create($data);

        if ($request->file('commercial_invoice')) {
            $fileName = time().$request->file('commercial_invoice')->getClientOriginalName();
            $commercial_invoice = $request->file('commercial_invoice')->storeAs('quotes', $fileName ,'public');
            $quote->commercial_invoice = '/storage/' . $commercial_invoice;
        }

        if ($request->file('parking_list')) {
            $fileName = time().$request->file('parking_list')->getClientOriginalName();
            $parking_list = $request->file('parking_list')->storeAs('quotes', $fileName ,'public');
            $quote->parking_list = '/storage/' . $parking_list;
        }

        $quote->save();
        return back()->with('success','File has been uploaded.');
    }

    public function index(): \Inertia\Response
    {
        $quotes = Quote::where('user_id', auth()->user()->id)->with('origin_country','origin_state',
            'origin_city', 'destination_country', 'destination_state', 'destination_city')->latest()->paginate(10);
        return Inertia::render('Quotes/Quotes', [
            'quotes' => $quotes
        ]);
    }

    public function adminQuotes(): \Inertia\Response
    {
        $quotes = Quote::with('origin_country','origin_state',
            'origin_city', 'destination_country', 'destination_state', 'destination_city', 'user')->latest()->paginate(10);
        return Inertia::render('Admin/Quotes/Quotes', [
            'quotes' => $quotes
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function setPrice(SetQuotePriceRequest $request, $id)
    {
        $quote = Quote::find($id);
        if (!$quote) throw ValidationException::withMessages(['amount' => 'The selected quote is invalid']);
        $quote->amount = $request->amount;
        $quote->save();
        return redirect()->back()->with('message', 'Quote price has been set successfully');
    }
}