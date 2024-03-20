<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Farmacia;

class FarmaciaSeeder extends Seeder
{

    public function run(): void
    {
       //Creamos 3 farmacias
       Farmacia::create(['nombre' => 'Farmacia A']);
       Farmacia::create(['nombre' => 'Farmacia B']);
       Farmacia::create(['nombre' => 'Farmacia C']);
    }
}