<?php

namespace App\Services;

use App\Http\Requests\PayInitializeRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Unicodeveloper\Paystack\Paystack;

class WalletServices
{
    public function logInitPayment()
    {

    }
    public function initializePay(PayInitializeRequest $request, PaystackServices $paystackServices)
    {
        //$data = $request->validated();

        try{
            $data = [
                "amount" => $request->amount * 100,
                "reference" => time(),
                "email" => auth()->user()->email,
                "currency" => "NGN"
            ];
            $response = $this->callPaystack($data);
            return json_decode($response, true)['data']['authorization_url'];
        }catch(\Exception $e) {
            return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
        }
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
