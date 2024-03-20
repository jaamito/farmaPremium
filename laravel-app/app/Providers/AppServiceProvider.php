<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AcumularPuntosService;
use App\Services\AcumularPuntosServiceInterface;
use App\Services\CanjearPuntosService;
use App\Services\CanjearPuntosServiceInterface;
use App\Services\ConsultaPuntosService;
use App\Services\ConsultaPuntosServiceInterface;
use App\Services\ValidarDatosService;
use App\Services\ValidarDatosServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AcumularPuntosServiceInterface::class, AcumularPuntosService::class);
        $this->app->bind(CanjearPuntosServiceInterface::class, CanjearPuntosService::class);
        $this->app->bind(ConsultaPuntosServiceInterface::class, ConsultaPuntosService::class);
        $this->app->bind(ValidarDatosServiceInterface::class, ValidarDatosService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
