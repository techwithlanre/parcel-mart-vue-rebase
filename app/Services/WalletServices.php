<?php

namespace App\Services;

use App\Http\Requests\PayInitializeRequest;
use App\Models\PaystackTransaction;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Unicodeveloper\Paystack\Paystack;

class WalletServices
{
    protected String $reference;
    private PaystackTransaction $paystackTransaction;

    private Transaction $transaction;
    public function logInitPayment(PayInitializeRequest $request)
    {
        $this->transaction = auth()->user()->deposit($request->amount, null, false);
        $data = [
            'user_id' => auth()->user()->id,
            'transaction_id' => $this->transaction->id,
            'reference' => $this->transaction->uuid,
            'status' => 'pending'
        ];

        $this->paystackTransaction = PaystackTransaction::create($data);
    }
    public function initializePay(PayInitializeRequest $request, PaystackServices $paystackServices)
    {
        $this->logInitPayment($request);
        try{
            $data = [
                "amount" => $request->amount * 100,
                "reference" => $this->transaction->uuid,
                "email" => auth()->user()->email,
                "currency" => "NGN",
                "callback_url" => route("wallet.paystack.webhook")
            ];

            $response = json_decode($this->callPaystack($data), true);
            $payment_url = $response['data']['authorization_url'];
            $this->paystackTransaction->status = 'processing';
            $this->paystackTransaction->save();
            return Inertia::location($payment_url);
        }catch(\Exception $e) {
            return Redirect::back()->withMessage(['error'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
        }
    }

    public function webhook(Request $request)
    {
        $payload = file_get_contents("php://input");
        $url_ref_1 = trim($request->trxref);
        $url_ref_2 = trim($request->reference);
        if ($url_ref_1 !== $url_ref_2) return false; //log to db
        $response = json_decode($payload, true);

        $paystack_transaction = PaystackTransaction::where('reference', $url_ref_1)->first();
        //if ($response['data']['status'] === 'success') {
            if ($paystack_transaction) {
                $user = auth()->user();
                $transaction = Transaction::whereId($paystack_transaction->transaction_id)->first();
                if (!$transaction->confirmed) $user->confirm($transaction);
                $paystack_transaction->status = 'success';
                $paystack_transaction->save();
                return \redirect(route('wallet.index'))->with('message', 'Wallet funded successfully');
            }
            return \redirect(route('wallet.index'))->with('message', 'An error occurred, please try again later');
        //}

        //$paystack_transaction->status = 'failed';
        //$paystack_transaction->save();
        //return \redirect(route('wallet.index'))->with('message', 'An error occurred, please try again later');
    }

    private function callPaystack($data)
    {
        $url = "https://api.paystack.co/transaction/initialize";
        $fields_string = http_build_query($data);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ".Config::get('paystack.secretKey'),
            "Cache-Control: no-cache",
            "Access-Control-Allow-Origin: https://checkout.paystack.com"
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        return $result;

    }
}
