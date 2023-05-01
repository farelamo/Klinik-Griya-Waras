<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
       return [
            'status' => 'success',
            'data'   => $this->resource
        ];
    }
}