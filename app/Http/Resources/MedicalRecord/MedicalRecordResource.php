<?php

namespace App\Http\Resources\MedicalRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status' => 'success',
            'data'   => [
                'id'            => $this->id,
                'patient'       => $this->patient->name,
                'doctor'        => $this->doctor->name,
                'complaint'     => $this->complaint,
                'diagnose'      => $this->diagnose,
                'diagnose'      => $this->diagnose,
                'normal_drugs'  => $this->normal_drugs->map(function ($n){
                                        $type_concoction = $n->normal_type_concoctions()
                                                             ->wherePivot('medical_record_id', $this->id)
                                                             ->first();
                                        return [
                                            'id'     => $n->id,
                                            'name'   => $n->name,
                                            'amount' => $n->pivot->amount,
                                            'times'  => $n->pivot->times,
                                            'dd'     => $n->pivot->dd,
                                            'dose'   => $n->pivot->dose,
                                            'type_concoction_id' => $type_concoction->id ?? null
                                        ];
                                    }),
                'mix_drugs'     => $this->mix_drugs->map(function ($m){

                                        $type_concoction = $m->mix_type_concoctions()
                                                             ->wherePivot('medical_record_id', $this->id)
                                                             ->first();

                                        return [
                                            'id'                 => $m->id,
                                            'name'               => $m->name,
                                            'amount'             => $m->pivot->amount,
                                            'times'              => $m->pivot->times,
                                            'dd'                 => $m->pivot->dd,
                                            'dose'               => $m->pivot->dose,
                                            'type_concoction_id' => $type_concoction->id ?? null
                                        ];
                                    }),
                'date'          => date('Y-m-d', strtotime($this->created_at)),
            ]
        ];
    }
}
