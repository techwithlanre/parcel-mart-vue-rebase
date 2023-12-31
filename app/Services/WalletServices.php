<?php

namespace App\Services;

use App\Http\Requests\PayInitializeRequest;
use App\Models\PaystackTransaction;
use App\Models\WalletOverdraft;
use App\Models\WalletTransaction;
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
    private WalletTransaction $walletTransaction;
    public function logInitPayment(PayInitializeRequest $request)
    {
        $this->transaction = auth()->user()->deposit($request->amount, null, false);
        $this->walletTransaction = WalletTransaction::create([
            'user_id' => auth()->user()->id,
            'transaction_id' => $this->transaction->id,
            'reference' => $this->transaction->uuid,
            'status' => 'pending',
            'amount' => $request->amount,
            'before' => auth()->user()->balance,
            'comment' => 'wallet funding',
            'description' => 'deposit',
            'currency' => 'NGN',
            'time_initiated' => now(),
            'channel' => 'paystack'
        ]);
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
            $this->walletTransaction->status = 'processing';
            $this->walletTransaction->save();
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
                "Authorization: Bearer sk_live_c07abf68f541c1dfa43d9260044dd47a7559bf87",
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
            $response = $this->verifyPayment($url_ref_1);
            $result = json_decode($response, true);

            if ($result['status']) {
                if ($result['data']['reference'] != $url_ref_1 && $result['data']['status'] != 'success') {
                    return \redirect()->with('error', 'An error occurred, Please contact support');
                }

                $transaction = Transaction::where('uuid', $url_ref_1)->first();
                $wallet_transaction = WalletTransaction::where([
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->uuid,
                ])->first();
                if ($wallet_transaction && $wallet_transaction->status == 'processing') {
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
                    $wallet_transaction->status = 'success';
                    $wallet_transaction->after = auth()->user()->balance;
                    $wallet_transaction->time_completed = now();
                    $wallet_transaction->save();
                    return \redirect(route('wallet.index'))->with('message', 'Wallet funded successfully');
                }

                return \redirect(route('wallet.index'))->with('message', 'Your wallet has been previously credited for this transaction.');
            }
        } catch (\Throwable $e) {

            return \redirect(route('wallet.index'))->with('message', 'An error occurred, please try again later');
        }


        return \redirect(route('wallet.index'))->with('message', 'An error occurred, please try again later');
    }

    private function callPaystack($data): bool|string
    {
        $url = "https://api.paystack.co/transaction/initialize";
        $fields_string = http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer sk_live_c07abf68f541c1dfa43d9260044dd47a7559bf87",
            "Cache-Control: no-cache",
            "Access-Control-Allow-Origin: https://checkout.paystack.com"
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        return curl_exec($ch);

    }
}
