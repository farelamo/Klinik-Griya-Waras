<?php

namespace App\Http\Resources\Drug;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DrugResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status' => 'success',
            'data'   => $this->resource
        ];
    }
}
