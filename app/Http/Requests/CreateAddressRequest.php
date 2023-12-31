<?php

namespace App\Http\Requests;
use App\Rules\AddressRule;
use App\Rules\BusinessNameRule;
use App\Rules\FullNameRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateAddressRequest extends FormRequest
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
            'contact_name' => ['required', 'string', new FullNameRule],
            'contact_email' => 'required|email:rfc,dns',
            'contact_phone' => 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'landmark' => 'required|max:45|min:3',
            'address_1' => ['required','string','max:45','min:3', new AddressRule],
            'address_2' => ['required','string','max:45','min:3', new AddressRule],
            'country_id' => 'required|numeric',
            'state_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'postcode' => 'required|min:4',
            'business_name' => ['nullable', new BusinessNameRule],
        ];
    }
}
