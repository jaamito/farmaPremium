<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PuntosController;
use App\Http\Controllers\CanjearPuntosController;
use App\Http\Controllers\ConsultaPuntosController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Definimos middleware 'guest' porque no hay auth, en el caso de tener auth creamos un middleware para verificar usuario.
Route::middleware('guest')->group(function () {
    Route::post('/acumular', [PuntosController::class, 'acumular']);
    Route::post('/canjear', [CanjearPuntosController::class, 'canjear']);
    Route::get('/consultar/puntos-periodo-farmacia', [ConsultaPuntosController::class, 'puntosPeriodoFarmacia']);
    Route::get('/consultar/puntos-farmacia-cliente', [ConsultaPuntosController::class, 'puntosFarmaciaCliente']);
    Route::get('/consultar/saldo-cliente', [ConsultaPuntosController::class, 'saldoCliente']);
});
