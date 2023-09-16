<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class BookShipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @param Request $bookShipmentRequest
     * @return bool
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
            'invoice_date' => 'nullable|date',
            'invoice_number' => 'nullable|min:4',
            'insurance' => 'required|exists:insurance_options,id',
            'shipment_date' => ['required','date','after:'.date('Y-m-d'),'before:'.date('Y-m-d', strtotime("+10 days"))],

        ];
    }

    public function messages(): array
    {
        return [
            'shipment_date.after' => "Planned shipment date must start from tomorrow",
            'shipment_date.before' => "Planned shipment date must not be more than 10 days",
        ];
    }
}
