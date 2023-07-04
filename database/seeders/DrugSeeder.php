<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Drug;
use App\Models\MedicalRecord;
use App\Models\TypeConcoction;

class DrugSeeder extends Seeder
{
    public function run(): void
    {
        $records = MedicalRecord::select('id')->get();

        Drug::factory()->count(10)->create()->each(function ($drug) use ($records){
            $drug->normal_drug_medical_records()->attach(
                $records->random(3), [
                    'amount'             => rand(1, 10),
                    'times'              => 3,
                    'dd'                 => 1,
                    'dose'               => rand(1, 20) . ' mg',
                    'type_concoction_id' => rand(1, 10)
                ]
            );

            $drug->mix_drug_medical_records()->attach(
                $records->random(2), [
                    'amount'             => rand(1, 10),
                    'times'              => 3,
                    'dd'                 => 1,
                    'dose'               => rand(1, 20) . ' mg',
                    'type_concoction_id' => rand(1, 10)
                ]
            );
        });
    }
}
