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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'country' => ['required'],
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
            'first_name.required'=>'OTP is required.',
            'last_name.required'=>'OTP is required.'
        ];
    }
}
