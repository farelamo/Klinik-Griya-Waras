<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data'    => $this->collection->transform(function ($data) {
                return [
                    'id'        => $data->id,
                    'name'      => $data->name,
                    'role'      => $data->role,
                    'gender'    => $data->gender,
                    'birth'     => $data->birth,
                    'address'   => $data->address,
                    'phone'     => $data->phone,
                ];
            })
        ];
    }
}
