<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class BusinessRegisterRequest extends FormRequest
{public function authorize(): bool
{
    return true;
}

    public function rules(): array
    {
        return [
            'business_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'country' => ['required']
        ];
    }

    public function messages(): array
    {
        return [

        ];
    }
}
