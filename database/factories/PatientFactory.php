<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        $gender         = ['L', 'P'];
        $choosen_gender = array_rand($gender);
        
        return [
            'name'       => fake()->name(),
            'gender'     => $gender[$choosen_gender],
            'birth'      => date('Y-m-d'),
            'address'    => fake()->address(),
            'phone'      => '08' . rand(1000000000,5000000000),
            'identifier' => rand(100000000000, 500000000000),
        ];
    }
}