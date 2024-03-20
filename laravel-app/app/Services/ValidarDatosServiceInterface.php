<?php

namespace App\Services;
use App\Models\Farmacia;
use App\Models\Cliente;

interface ValidarDatosServiceInterface
{
    public function validarFarmacia(int $idFarmacia): ?Farmacia;
    public function validarCliente(int $idCliente): ?Cliente;
}