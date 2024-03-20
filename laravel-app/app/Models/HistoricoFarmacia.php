<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoFarmacia extends Model
{
    protected $table = 'historicoFarmacia';

    protected $fillable = [
        'farmacia_id',
        'cliente_id',
        'puntos_otorgados',
    ];
}
