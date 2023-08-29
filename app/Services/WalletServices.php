<?php

namespace App\Services;

use App\Http\Requests\PayInitializeRequest;
use App\Models\PaystackTransaction;
use App\Models\WalletOverdraft;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

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

    public function initializePay(PayInitializeRequest $request)
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

    private function verifyPayment($reference): bool|string
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer sk_test_4a32e9e6a564687a548afd047f0429740edc267e",
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return false;
        }

        return $response;
    }

    public function webhook(Request $request)
    {
        //http://127.0.0.1:8000/wallet/callback?trxref=8756a2d1-8aa4-43ca-8974-42db7ce8f9f9&reference=8756a2d1-8aa4-43ca-8974-42db7ce8f9f9
        $url_ref_1 = trim($request->trxref);
        $url_ref_2 = trim($request->reference);
        if ($url_ref_1 !== $url_ref_2) {
            return \redirect()->with('error', 'An error occurred, Please contact support');
        } //log to db

        try {

        } catch (\Throwable $e) {

        }
        $response = $this->verifyPayment($url_ref_1);
        $result = json_decode($response, true);

        if ($result['status']) {
            if ($result['data']['reference'] != $url_ref_1 && $result['data']['status'] != 'success') {
               return \redirect()->with('error', 'An error occurred, Please contact support');
            }

            $paystack_transaction = PaystackTransaction::where('reference', $url_ref_1)->first();
            $transaction = Transaction::whereId($paystack_transaction->transaction_id)->first();
            if ($paystack_transaction && $paystack_transaction['status'] == 'processing') {
                $user = auth()->user();
                $funding_amount = $transaction->amount;
                $new_funding_amount = 0;
                $overdraft = WalletOverdraft::where('user_id', $user->id)->first();
                if ($overdraft) {
                    $overdraft_balance = $overdraft->balance;
                    $new_overdraft_balance = $overdraft_balance;
                    if ($overdraft_balance > 0) {
                        if ($overdraft_balance >= $funding_amount) {
                            $new_overdraft_balance = $overdraft_balance - $funding_amount;
                            $funding_amount = 0;
                        }

                        if ($funding_amount >= $overdraft_balance) {
                            $funding_amount = $funding_amount - $overdraft_balance;
                            $new_overdraft_balance = 0;
                        }
                    }

                    $transaction->amount = $funding_amount;
                    $transaction->save();
                    $overdraft->balance = $new_overdraft_balance;
                    $overdraft->save();
                }

                if (!$transaction->confirmed) $user->confirm($transaction);
                $paystack_transaction->status = 'success';
                $paystack_transaction->save();
                return \redirect(route('wallet.index'))->with('message', 'Wallet funded successfully');
            }



            return \redirect(route('wallet.index'))->with('message', 'Your wallet has been previously credited for this transaction.');
        }

        return \redirect(route('wallet.index'))->with('message', 'An error occurred, please try again later');
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
            "Authorization: Bearer sk_test_4a32e9e6a564687a548afd047f0429740edc267e",
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
