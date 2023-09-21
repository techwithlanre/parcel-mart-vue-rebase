<?php

namespace App\Http\Requests;

use App\Rules\FullNameRule;
use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateQuoteRequest extends FormRequest
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
            'user_id' => ['nullable', 'exists:user,id'],
            'name' => ['nullable', new FullNameRule],
            'email' => ['nullable', 'email:rfc,dns'],
            'phone' => ['nullable', 'numeric'],
            'country_from' => ['required', 'exists:countries,id'],
            'state_from' => ['required', 'exists:states,id'],
            'city_from' => ['required','exists:cities,id'],
            'country_to' => ['required', 'exists:countries,id'],
            'state_to' => ['required', 'exists:states,id'],
            'city_to' => ['required', 'exists:cities,id'],
            'quantity' => ['required','numeric'],
            'weight' => ['required', 'numeric'],
            'length' => ['required','numeric'],
            'width' => ['required','numeric'],
            'height' => ['required','numeric'],
            'commercial_invoice' => ['nullable','mimes:csv,txt,xlx,xls,pdf','max:2048'],
            'parking_list' => ['nullable','mimes:csv,txt,xlx,xls,pdf','max:2048'],
        ];
    }
}
