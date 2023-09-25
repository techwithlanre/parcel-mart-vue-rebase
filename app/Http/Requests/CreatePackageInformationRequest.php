<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePackageInformationRequest extends FormRequest
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
            'category' => ['required', 'exists:item_categories,id'],
            'description' => ['required', ''],
            'quantity' => ['required', 'numeric'],
            'weight' => ['required', 'between:0,99.99'],
            'height' => ['required', 'between:0,99.99'],
            'length' => ['required', 'between:0,99.99'],
            'width' => ['required', 'between:0,99.99'],
            'value' => ['required', 'between:0,99.99'],
        ];
    }
}
