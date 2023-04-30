<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'stock'];

    public function normal_drug_medical_records()
    {
        return $this->belongsToMany(MedicalRecord::class, 'normal_drugs')
                    ->withPivot('amount', 'times', 'dd', 'medical_record_id')
                    ->withTimestamps();
    }

    public function mix_drug_medical_records()
    {
        return $this->belongsToMany(MedicalRecord::class, 'mix_drugs')
                    ->withPivot('amount', 'times', 'dd', 'medical_record_id', 'type_concoction_id')
                    ->withTimestamps();
    }

    public function type_concoctions()
    {
        return $this->belongsToMany(TypeConcoction::class, 'mix_drugs')
                    ->withPivot('amount', 'times', 'dd', 'medical_record_id', 'type_concoction_id')
                    ->withTimestamps();
    }
}