<?php

namespace App\Services;

use App\Models\Farmacia;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

class ValidarDatosService implements ValidarDatosServiceInterface
{
    public function validarFarmacia(int $idFarmacia): ?Farmacia
    {
        return Farmacia::find($idFarmacia);
    }
    public function validarCliente(int $idCliente): ?Cliente
    {
        return Cliente::find($idCliente);
    }
}