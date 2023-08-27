<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UserRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:255|alpha',
            'last_name' => 'required|string|max:255|alpha',
            'phone' => 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:11|unique:users',
            'email' => 'required|string|email:rfc,dns|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'country' => ['required', 'exists:countries,id'],
            'ref_by' => ['nullable', 'exists:users,ref_code'],
        ];

        /*if ($this->request->has('ref_by')) {
            $rules['ref_by'] = [];
        }*/

        return $rules;
    }

    public function messages(): array
    {
        return [

        ];
    }
}
