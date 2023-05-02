<?php

namespace App\Http\Resources\Receipt;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReceiptCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data'    => $this->collection->transform(function ($data) {
                return [
                    'id'            => $data->id,
                    'patient_id'    => $data->patient->name,
                    'doctor_id'     => $data->doctor->name,
                    'diagnose'      => $data->diagnose,
                    'normal_drugs'  => $data->normal_drugs->map(function ($n){
                                            return [
                                                'name'   => $n->name,
                                                'amount' => $n->pivot->amount,
                                                'times'  => $n->pivot->times,
                                                'dd'     => $n->pivot->dd,
                                            ];
                                       }),
                    'mix_drugs'     => $data->mix_drugs->map(function ($m) use ($data){
                                            return [
                                                'name'      => $m->name,
                                                'amount'    => $m->pivot->amount,
                                                'times'     => $m->pivot->times,
                                                'dd'        => $m->pivot->dd,
                                                'type'      => $m->type_concoctions()
                                                                 ->wherePivot('medical_record_id', $data->id)
                                                                 ->first()->name,
                                            ];
                                       }),
                    'date'          => date('Y-m-d', strtotime($data->created_at)),
                ];
            })
        ];
    }
}
