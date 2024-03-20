<?php

namespace App\Services;

use App\Models\Movimiento;
use App\Models\HistoricoFarmacia;
use App\Models\Tarjeta;
use Illuminate\Support\Facades\DB;

class AcumularPuntosService implements AcumularPuntosServiceInterface
{
    public function acumularPuntos(int $idFarmacia, int $idCliente, int $puntos): void
    {
        
        //lógica para crear una acumulación de puntos
        DB::beginTransaction();

        try
        {
            $tarjeta = Tarjeta::where('cliente_id', $idCliente)->first();

            if(!$tarjeta)
            {
                $tarjeta = Tarjeta::create([
                    'cliente_id' => $idCliente,
                    'saldo_actual' => 0
                ]);
            }
    
            Movimiento::create([
                'farmacia_id' => $idFarmacia,
                'cliente_id' => $idCliente,
                'puntos_acumulados' => $puntos,
                'puntos_canjeados' => 0
            ]);

            
            $historicoFarmacia = HistoricoFarmacia::where('farmacia_id', $idFarmacia)->where('cliente_id', $idCliente)->first();

            if(!$historicoFarmacia)
            {
                $historicoFarmacia = HistoricoFarmacia::create([
                    'farmacia_id' => $idFarmacia,
                    'cliente_id' => $idCliente,
                    'puntos_otorgados' => 0,
                ]);
            }

            $historicoFarmacia->increment('puntos_otorgados', $puntos); 
            $tarjeta->increment('saldo_actual', $puntos); 

            DB::commit();
        }catch (\Exception $e){

            DB::rollback();
            throw new AcumulacionPuntosException('Ocurrió un error al acumular puntos.', 500);
        }

    }
}



