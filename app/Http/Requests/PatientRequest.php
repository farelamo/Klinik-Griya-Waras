<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|max:255',
            'gender'     => 'required|in:L,P',
            'birth'      => 'required|date|date_format:Y-m-d',
            'address'    => 'required',
            'phone'      => 'required|max:13',
        ];
    }

    public function messages()
    {
        return [
            'name.required'       => 'name must be filled',
            'name.max'            => 'maximum of name is 255 character',
            'gender.required'     => 'gender must be filled',
            'gender.in'           => "gender doesn't exist",
            'birth.required'      => 'birthdate must be filled',
            'birth.date'          => 'birthdate must be type of date',
            'birth.date_format'   => 'birthdate format is Y-m-d',
            'address.required'    => 'address must be filled',
            'phone.required'      => 'phone must be filled',
            'phone.max'           => 'maximum of phone is 13 number',
        ];
    }
}