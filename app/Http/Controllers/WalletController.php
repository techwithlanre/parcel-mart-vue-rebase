<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayInitializeRequest;
use App\Models\User;
use App\Services\PaystackServices;
use App\Services\WalletServices;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Unicodeveloper\Paystack\Paystack;

class WalletController extends Controller
{

    public function index()
    {
        $balance = number_format(auth()->user()->balance, 2);
        $user = User::whereId(auth()->user()->id)->first();
        $transactions = Transaction::where([
            'payable_id' => $user->id,
            'confirmed' => 1
        ])->orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('Wallet/Index', compact('balance','transactions'));
    }

    public function filterTransactions(Request $request): \Illuminate\Http\JsonResponse
    {
        $transactions = Transaction::where([
            'payable_id' => auth()->user()->id,
            'confirmed' => 1
        ])->where(function ($query) use ($request) {
            $query->when($request->filled('type'), function ($query) use ($request) {
                return $request->type == 'all'
                    ? $query
                    : $query->where('type', $request->type);
            });
        })->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($transactions);
    }

    public function initialize(PayInitializeRequest $request, WalletServices $services, PaystackServices $paystackServices)
    {
        return $services->initializePay($request, $paystackServices);
    }

    public function paystackWebhook(Request $request, WalletServices $services)
    {
        return $services->webhook($request);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
