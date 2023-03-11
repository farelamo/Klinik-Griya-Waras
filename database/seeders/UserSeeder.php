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
            'password'  => bcrypt('rahasiabro'),
        ]);

        User::factory()->count(50)->create();
    }
}
