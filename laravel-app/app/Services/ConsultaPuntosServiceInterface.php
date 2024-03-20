<?php

namespace App\Services;
use App\Models\Movimiento;
use App\Models\Tarjeta;

interface ConsultaPuntosServiceInterface
{
    public function consultaPuntosPeriodoFarmacia(int $idFarmacia, string $dateFrom, string $dateTo): ?array;
    public function consultaPuntosFarmaciaCliente(int $idFarmacia, int $idCliente): ?array;
    public function consultaSaldoCliente(int $idCliente): ?array;
}