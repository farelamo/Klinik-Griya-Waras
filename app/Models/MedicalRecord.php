<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'complaint', 'doctor_id', 'diagnose', 'drugs'];

    public function Patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function Doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
