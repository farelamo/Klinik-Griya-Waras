<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PatientCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data'    => $this->collection->transform(function ($data) {
                return [
                    'id'         => $data->id,
                    'name'       => $data->name,
                    'gender'     => $data->gender,
                    'birth'      => $data->birth,
                    'address'    => $data->address,
                    'phone'      => $data->phone,
                    'identifier' => $data->identifier,
                ];
            })
        ];
    }
}