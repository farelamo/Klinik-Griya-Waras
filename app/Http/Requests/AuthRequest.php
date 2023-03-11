<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'password' => 'required|max:8',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'     => 'email must be filled',
            'email.email'        => 'invalid email format',
            'password.required'  => 'password must be filled',
            'password.max'       => 'maximal password is 8 character',
        ];
    }
}
