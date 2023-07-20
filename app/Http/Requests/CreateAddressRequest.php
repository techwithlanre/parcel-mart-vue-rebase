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
            'contact_name' => 'required',
            'contact_email' => 'required',
            'contact_phone' => 'required',
            'business_name' => '',
            'landmark' => 'required|max:45|min:3',
            'address' => 'required|max:45|min:3',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'postcode' => ''
        ];
    }
}
