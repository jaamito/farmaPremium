<?php

namespace App\Services;

interface AcumularPuntosServiceInterface
{
    public function acumularPuntos(int $idFarmacia, int $idCliente, int $puntos): void;
}