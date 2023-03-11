<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DrugFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => fake()->firstName(),
            'description' => fake()->realTextBetween(),
            'stock'       => rand(100, 1000),  
        ];
    }
}
