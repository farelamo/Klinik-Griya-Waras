<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'gender', 'birth', 
        'address', 'phone', 'identifier',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($patient){

            $patient->medical_records()->delete();

            /* pake each biar soft delete pivotnya tdk cuma 1 record aja */
            // $patient->medical_records->each->delete();
        });
    }

    public function medical_records()
    {
        return $this->hasMany(MedicalRecord::class);
    }
}