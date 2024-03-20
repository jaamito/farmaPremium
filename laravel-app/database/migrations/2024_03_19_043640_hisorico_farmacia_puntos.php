<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('historicoFarmacia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farmacia_id');
            $table->foreign('farmacia_id')->references('id')->on('farmacias')->onDelete('cascade');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->integer('puntos_otorgados');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('historicoFarmacia');
    }
};
