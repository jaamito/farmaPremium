<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{

    public function run(): void
    {
       //Creamos 3 clientes
       Cliente::create(['nombre' => 'Cliente 1']);
       Cliente::create(['nombre' => 'Cliente 2']);
       Cliente::create(['nombre' => 'Cliente 3']);
    }
}