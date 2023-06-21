<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateShipmentRequest extends FormRequest
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
            'shipper_address_id' => 'required',
            'destination_address_id' => 'required',
            'weight' => 'required',
            'length' => '',
            'width' => '',
            'height' => '',
            'quantity' => 'required'
        ];
    }
}
