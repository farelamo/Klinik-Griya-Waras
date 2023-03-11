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
                $time = strtotime($data->created_at);
                $date = date('Y-m-d', $time);

                return [
                    'id'          => $data->id,
                    'patient_id'  => $data->patient->name,
                    'complaint'   => $data->complaint,
                    'doctor_id'   => $data->doctor->name,
                    'diagnose'    => $data->diagnose,
                    'drugs'       => json_decode($data->drugs),
                    'date'        => $date,
                ];
            })
        ];
    }
}
