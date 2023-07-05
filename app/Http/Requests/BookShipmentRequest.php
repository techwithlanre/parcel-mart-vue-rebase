<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookShipmentRequest extends FormRequest
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
            'option_id' => 'required|exists:shipping_rate_logs,id',
            'shipment_id' => 'required|exists:shipments,id',
            'insurance' => 'exists:insurance_options,id'
        ];
    }
}
