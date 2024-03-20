<?php

use App\Http\Controllers\PuntosController;
use App\Services\AcumularPuntosServiceInterface;
use App\Services\ValidarDatosServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Models\Farmacia;
use App\Models\Cliente;

class AcumularTest extends TestCase
{
    protected $acumularPuntosServiceMock;
    protected $validadorApiServiceMock;
    protected $puntosController;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear mocks para los servicios
        $this->acumularPuntosServiceMock = Mockery::mock(AcumularPuntosServiceInterface::class);
        $this->validadorApiServiceMock = Mockery::mock(ValidarDatosServiceInterface::class);

        // Crear una instancia del controlador con los mocks de servicios
        $this->puntosController = new PuntosController(
            $this->acumularPuntosServiceMock,
            $this->validadorApiServiceMock
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testAcumularConParametrosValidos()
    {
        // Configurar el mock para validarFarmacia
        $this->validadorApiServiceMock->shouldReceive('validarFarmacia')
        ->andReturn(Mockery::mock(Farmacia::class))
        ->shouldReceive('validarFarmacia')
        ->andReturn(null);

        // Configurar el mock para validarCliente
        $this->validadorApiServiceMock->shouldReceive('validarCliente')
        ->andReturn(Mockery::mock(Cliente::class))
        ->shouldReceive('validarCliente')
        ->andReturn(null);

        // Configurar el mock para acumularPuntos
        $this->acumularPuntosServiceMock->shouldReceive('acumularPuntos')->once();

        // Crear una instancia de Request con los parámetros válidos
        $request = new Request([
            'idFarmacia' => 1,
            'idCliente' => 1,
            'puntos' => 100,
        ]);

        // Ejecutar el método acumular del controlador
        $response = $this->puntosController->acumular($request);

        // Verificar la respuesta esperada
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Puntos acumulados correctamente', $response->getData()->Mensaje);
    }
}