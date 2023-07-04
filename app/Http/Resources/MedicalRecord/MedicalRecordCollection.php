<?php

namespace App\Http\Resources\MedicalRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MedicalRecordCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data'    => $this->collection->transform(function ($data) {
                return [
                    'id'            => $data->id,
                    'identifier'    => $data->patient->identifier,
                    'patient_id'    => $data->patient->name,
                    'complaint'     => $data->complaint,
                    'doctor_id'     => $data->doctor->name,
                    'diagnose'      => $data->diagnose,
                    'normal_drugs'  => $data->normal_drugs->map(function ($n) use ($data){
                                            $type_concoction = $n->normal_type_concoctions()
                                                                 ->wherePivot('medical_record_id', $data->id)
                                                                 ->first();
                                            return [
                                                'name'   => $n->name,
                                                'amount' => $n->pivot->amount,
                                                'times'  => $n->pivot->times,
                                                'dd'     => $n->pivot->dd,
                                                'dose'   => $n->pivot->dose,
                                                'type_concoction_id' => [
                                                        'id'   => $type_concoction->id ?? null,
                                                        'name' => $type_concoction->name ?? null,
                                                ]
                                            ];
                                       }),
                    'mix_drugs'     => $data->mix_drugs->map(function ($m) use ($data){
                                            $type_concoction = $m->mix_type_concoctions()
                                                                 ->wherePivot('medical_record_id', $data->id)
                                                                 ->first();
                                            return [
                                                'name'               => $m->name,
                                                'amount'             => $m->pivot->amount,
                                                'times'              => $m->pivot->times,
                                                'dd'                 => $m->pivot->dd,
                                                'dose'               => $m->pivot->dose,
                                                'type_concoction_id' => [
                                                                            'id'   => $type_concoction->id ?? null,
                                                                            'name' => $type_concoction->name ?? null,
                                                                        ]
                                            ];
                                       }),
                    'date'          => date('Y-m-d', strtotime($data->created_at)),
                ];
            })
        ];
    }
}
