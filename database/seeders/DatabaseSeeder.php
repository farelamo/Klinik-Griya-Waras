<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\DrugSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        return $this->call([
            UserSeeder::class,
            DrugSeeder::class,
        ]);
    }
}
