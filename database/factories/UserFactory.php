<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $gender         = ['L', 'P'];
        $choosen_gender = array_rand($gender);

        $role         = ['admin', 'doctor', 'patient'];
        $choosen_role = array_rand($role);

        return [
            'name'      => fake()->name(),
            'email'     => fake()->unique()->safeEmail(),
            'role'      => $role[$choosen_role],
            'gender'    => $gender[$choosen_gender],
            'birth'     => date('Y-m-d'),
            'address'   => fake()->address(),
            'phone'     => '08' . rand(1000000000,5000000000),
            'password'  => bcrypt('rahasiabro'),
        ];
    }
}
