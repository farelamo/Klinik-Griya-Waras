<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TypeConcoctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:100'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'type concoction name must be filled',
            'name.max'      => 'maximal type concoction is 100 character',
        ];
    }
}