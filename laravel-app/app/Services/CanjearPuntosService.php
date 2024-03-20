<?php

namespace App\Services;

use App\Models\Movimiento;
use App\Models\Tarjeta;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CanjearPuntosService implements CanjearPuntosServiceInterface
{
    public function canjearPuntos(int $idFarmacia, int $idCliente, int $puntos): ?array
    {
        // Verificar saldo del cliente
        DB::beginTransaction();
        $response = [];
        
        try{

            $tarjetaCliente = Tarjeta::where('cliente_id', $idCliente)->first();

            if ($tarjetaCliente->saldo_actual < $puntos) 
            {   
                $response["status"] = 400;
                $response["message"] = 'No tienes suficientes puntos para canjear';
                return $response;
            }

            $puntosAcumulados = Movimiento::where('cliente_id', $idCliente)
                ->whereColumn('puntos_canjeados', '<', 'puntos_acumulados')
                ->orderBy('created_at', 'asc')
                ->get();

            $puntosACanjear = $puntos;

            foreach ($puntosAcumulados as $regitro) 
            {
                $puntosDisponibles = $regitro->puntos_acumulados - $regitro->puntos_canjeados;

                if($puntosACanjear > 0 && $puntosDisponibles > 0)
                {
                    if($puntosACanjear > $puntosDisponibles)
                    {
                        $puntosACanjear -= $puntosDisponibles;
                        $regitro->increment('puntos_canjeados', $puntosDisponibles);
                    }elseif($puntosACanjear > 0)
                    {   
                        $regitro->increment('puntos_canjeados', $puntosACanjear);
                        $puntosACanjear = 0;
                    }
                    
                } else
                {
                    break;
                }
            }

            // Actualizar el saldo del cliente
            $tarjetaCliente->saldo_actual -= $puntos;
            $tarjetaCliente->save();
            DB::commit();

            $response["status"] = 200;
            $response["message"] = 'Puntos canjeados correctamente';
            return $response;
            

        }catch (\Exception $e){

            DB::rollback();
            $response["status"] = 500;
            $response["message"] = 'Error interno del servidor, contacta con el administrador de la API';
            return $response;
        }
    }
}
