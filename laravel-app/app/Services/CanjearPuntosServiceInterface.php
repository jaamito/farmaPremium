<?php

namespace App\Services;
use Illuminate\Http\JsonResponse;

interface CanjearPuntosServiceInterface
{
    public function canjearPuntos(int $idFarmacia, int $idCliente, int $puntos): ?array;
}