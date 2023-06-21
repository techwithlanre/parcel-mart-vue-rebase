<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

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
            'contact_name' => '',
            'contact_email' => '',
            'contact_phone' => '',
            'business_name' => '',
            'landmark' => '',
            'address' => '',
            'country_id' => '',
            'state_id' => '',
            'city_id' => '',
            'postcode' => ''
        ];
    }
}
