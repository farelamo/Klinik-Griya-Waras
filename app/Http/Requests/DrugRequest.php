<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DrugRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|max:255',
            'description' => 'required',
            'stock'       => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'drug name must be filled',
            'name.max'             => 'maximum of drug name is 255 character',
            'description.required' => 'description of drug must be filled',
            'stock.required'       => 'stock of drug must be filled',
            'stock.numeric'        => 'stock of drug must be numeric',
        ];
    }
}
