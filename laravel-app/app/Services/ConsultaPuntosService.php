<?php

namespace App\Services;

use App\Models\HistoricoFarmacia;
use App\Models\Movimiento;
use App\Models\Tarjeta;
use App\Models\Farmacia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ConsultaPuntosService implements ConsultaPuntosServiceInterface
{
    public function consultaPuntosPeriodoFarmacia(int $idFarmacia, string $dateFrom, string $dateTo): ?array
    {
        $returnPuntosFarmacia = [];
        try
        {
            $historico = HistoricoFarmacia::where('farmacia_id', $idFarmacia)->first();

            if ($historico) {
                $returnPuntosFarmacia["PuntosOtorgados"] = $historico->puntos_otorgados;
            }else{
                $returnPuntosFarmacia["PuntosOtorgados"] = 0;
            }

            $puntosNoCanjeados = Movimiento::whereRaw('puntos_canjeados < puntos_acumulados')
                ->where('farmacia_id', $idFarmacia)
                ->selectRaw('SUM(puntos_acumulados - puntos_canjeados) as total_puntos_no_canjeados')
                ->first();

            if ($puntosNoCanjeados->total_puntos_no_canjeados) {
                $returnPuntosFarmacia["PuntosNoCanjeados"] = $puntosNoCanjeados->total_puntos_no_canjeados;
            }else{
                $returnPuntosFarmacia["PuntosNoCanjeados"] = 0;
            }

            $nombreFarmacia = Farmacia::where('id', $idFarmacia)->value('nombre');
            $returnPuntosFarmacia["nombre"] = $nombreFarmacia;

            return $returnPuntosFarmacia;
    
        }catch (\Exception $e){
            return $returnPuntosFarmacia["error"] = $e->getMessage();
        }

    }

    public function consultaPuntosFarmaciaCliente(int $idFarmacia, int $idCliente): ?array
    {
        try
        {
            $puntosFarmaciaCliente = Movimiento::join('clientes', 'movimientos.cliente_id', '=', 'clientes.id')
                ->join('farmacias', 'movimientos.farmacia_id', '=', 'farmacias.id')
                ->where('movimientos.farmacia_id', $idFarmacia)
                ->where('movimientos.cliente_id', $idCliente)
                ->select('clientes.nombre as nombre_cliente', 'farmacias.nombre as nombre_farmacia', DB::raw('SUM(movimientos.puntos_acumulados) as total_puntos'))
                ->groupBy('clientes.nombre', 'farmacias.nombre')
                ->first();

            if(!$puntosFarmaciaCliente){
                return [];
            }else{
                return $puntosFarmaciaCliente->toArray();
            }
                
        }catch (\Exception $e){
            return $returnPuntosFarmacia["error"] = $e->getMessage();
        }
    }

    public function consultaSaldoCliente(int $idCliente): ?array
    {
        try
        {
        
            $saldoCliente = Tarjeta::join('clientes', 'tarjetas.cliente_id', '=', 'clientes.id')
            ->where('tarjetas.cliente_id', $idCliente)
            ->select('tarjetas.saldo_actual', 'clientes.nombre as nombre_cliente')
            ->first();

            if(!$saldoCliente){
                return [];
            }else{
                return $saldoCliente->toArray();
            }
        }catch (\Exception $e){
            return $returnPuntosFarmacia["error"] = $e->getMessage();
        }
    }
}