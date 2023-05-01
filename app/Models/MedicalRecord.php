<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['patient_id', 'complaint', 'doctor_id', 'diagnose', 'pharmacist'];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function normal_drugs()
    {
        return $this->belongsToMany(Drug::class, 'normal_drugs')
                    ->withPivot('amount', 'times', 'dd', 'drug_id', 'deleted_at')
                    ->withTimestamps();
    }

    public function mix_drugs()
    {
        return $this->belongsToMany(Drug::class, 'mix_drugs')
                    ->withPivot('amount', 'times', 'dd', 'drug_id', 'type_concoction_id', 'deleted_at')
                    ->withTimestamps();
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::deleting(function($medical_record){

    //         $mix    = $medical_record->mix_drugs()->get();
    //         $normal = $medical_record->normal_drugs()->get();
            
    //         if(!empty($mix->toArray())):
                
    //             foreach ($mix as $data) {
    //                 $medical_record->mix_drugs()->updateExistingPivot(
    //                     $data->id, ['deleted_at' => Carbon::now()]
    //                 );
    //             }
    //         endif;

    //         if(!empty($normal->toArray())):
                
    //             foreach ($normal as $data) {
    //                 $medical_record->normal_drugs()->updateExistingPivot(
    //                     $data->id, ['deleted_at' => Carbon::now()]
    //                 );
    //             }
    //         endif;
    //     });
    // }
}