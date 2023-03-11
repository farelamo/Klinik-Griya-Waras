<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complaint'   => 'required',
            'diagnose'    => 'required',
            'drugs'       => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            'complaint.required'  => 'complaint must be filled',
            'diagnose.required'   => 'diagnose must be filled',
            'drugs.required'      => 'drugs must be filled',
            'drugs.array'         => 'drugs must be type of array',
        ];
    }
}
