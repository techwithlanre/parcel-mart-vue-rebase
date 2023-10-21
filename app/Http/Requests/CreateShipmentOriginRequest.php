<?php

namespace App\Http\Requests;

use App\Rules\AddressRule;
use App\Rules\BusinessNameRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateShipmentOriginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'shipment_id' => ['nullable', 'exists:shipments,id'],
            'contact_name' => ['required', 'max:255'],
            //'contact_email' => 'required|email:rfc,dns',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'landmark' => 'required|max:45|min:3',
            'address_1' => ['required','max:45','min:3', new AddressRule],
            'address_2' => ['required','max:45','min:3', new AddressRule],
            //'business_name' => ['nullable', new BusinessNameRule],
            'business_name' => ['nullable'],
            'country_id' => 'required|numeric|exists:countries,id',
            'state_id' => 'required|numeric|exists:states,id',
            'city_id' => 'required|numeric|exists:cities,id',
            'postcode' => 'required|min:2'
        ];
    }

    public function messages()
    {
        return [
            'country_id.exists' => "Choose a country from the dropdown",
            'country_id.required' => "Choose a country from the dropdown",
        ];
    }
}
