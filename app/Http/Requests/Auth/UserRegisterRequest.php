<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
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
        return [
            'first_name' => 'required|string|max:255|alpha',
            'last_name' => 'required|string|max:255|alpha',
            'phone' => 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users',
            'email' => 'required|string|email:rfc,dns|max:255|unique:'.User::class,
            'dob' => 'required|date',
            'gender' => 'required|string',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'country' => ['required', 'exists:countries,id'],
            'ref_by' => ['nullable', 'exists:users,ref_code'],
        ];
    }

    public function messages(): array
    {
        return [

        ];
    }

    public function attributes(): array
    {
        return [
            'dob' => 'date of birth'
        ];
    }
}
