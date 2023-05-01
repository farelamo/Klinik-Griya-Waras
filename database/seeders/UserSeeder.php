<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'      => 'superadmin',
            'email'     => 'superadmin@gmail.com',
            'role'      => 'superadmin',
            'gender'    => 'L',
            'birth'     => date('Y-m-d'),
            'address'   => 'Jl. Arteri Ringroad Utara Gejayan No.6, Sanggrahan, Condongcatur, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta',
            'phone'     => '08' . rand(1000000000,5000000000),
            'password'  => bcrypt('rahasia'),
        ]);

        User::factory()->count(10)->sequence(
                ['role' => 'admin'],
                ['role' => 'pharmacist'],
            )->create();

        /* 
            Kuncinya ada di model yang function relasi (return $this->belongsTo, dll) 
            untuk keperluan custom nama tabel pivotnya sama custom nama field serta
            mengisi field timestamp (->withTimestamps())
        */
        
        User::factory()->count(10)
            ->state(['role' => 'doctor'])
            ->hasAttached(
                Patient::factory()->count(3),
                [
                    'pharmacist' => (bool) mt_rand(0, 1),
                    'complaint'  => fake()->realTextBetween(),
                    'diagnose'   => fake()->realTextBetween(),
                ]
            )
            ->create();
    }
}