<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role', 
        'gender', 'birth', 'address', 'phone',
        'identifier',
    ];

    protected $hidden = ['password'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function medical_records()
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id', 'id');
    }

    /*
        Buat seeding (kunci seeding with relation many to many)
        atau juga bisa dipake di service
    */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'medical_records', 'doctor_id', 'patient_id')->withTimestamps();
    }
}