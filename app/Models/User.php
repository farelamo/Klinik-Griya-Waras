<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 
        'gender', 'birth', 'address', 'phone'
    ];

    protected $hidden   = ['password'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function PatientMedicalRecords()
    {
        return $this->hasMany(MedicalRecords::class, 'patient_id', 'id');
    }

    public function DoctorMedicalRecords()
    {
        return $this->hasMany(MedicalRecords::class, 'doctor_id', 'id');
    }
}
