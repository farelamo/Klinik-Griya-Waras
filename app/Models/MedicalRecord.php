<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'complaint', 'doctor_id', 'diagnose'];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function normal_drugs()
    {
        return $this->belongsToMany(Drug::class, 'normal_drugs')
                    ->withPivot('amount', 'times', 'dd', 'drug_id')
                    ->withTimestamps();
    }

    public function mix_drugs()
    {
        return $this->belongsToMany(Drug::class, 'mix_drugs')
                    ->withPivot('amount', 'times', 'dd', 'drug_id', 'type_concoction_id')
                    ->withTimestamps();
    }
}