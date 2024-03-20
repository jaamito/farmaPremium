<?php

use App\Http\Controllers\ConsultaPuntosController;
use App\Services\ConsultaPuntosServiceInterface;
use App\Services\ValidarDatosServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Models\Farmacia;
use App\Models\Cliente;

class ConsultarTest extends TestCase
{
    protected $consultaPuntosServiceMock;
    protected $validadorApiServiceMock;
    protected $consultaPuntosController;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear mocks para los servicios
        $this->consultaPuntosServiceMock = Mockery::mock(ConsultaPuntosServiceInterface::class);
        $this->validadorApiServiceMock = Mockery::mock(ValidarDatosServiceInterface::class);

        // Crear una instancia del controlador con los mocks de servicios
        $this->consultaPuntosController = new ConsultaPuntosController(
            $this->consultaPuntosServiceMock,
            $this->validadorApiServiceMock
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testConsultaPuntosPeriodoFarmaciaConParametrosValidos()
    {
        // Configurar el mock para validarFarmacia
        $this->validadorApiServiceMock->shouldReceive('validarFarmacia')
            ->andReturn(Mockery::mock(Farmacia::class));

        // Crear una instancia de Request con los parámetros válidos
        $request = new Request([
            'idFarmacia' => 1,
            'dateFrom' => '2024-03-01 00:00:00',
            'dateTo' => '2024-03-31 23:59:59',
        ]);

        // Configurar el mock para consultaPuntosPeriodoFarmacia
        $this->consultaPuntosServiceMock->shouldReceive('consultaPuntosPeriodoFarmacia')
            ->andReturn([
                'PuntosOtorgados' => 100,
                'PuntosNoCanjeados' => 80,
                'nombre' => 'Farmacia ABC',
            ]);

        // Ejecutar el método puntosPeriodoFarmacia del controlador
        $response = $this->consultaPuntosController->puntosPeriodoFarmacia($request);

        // Verificar la respuesta esperada
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $response->getData()->Id);
        $this->assertEquals('Farmacia ABC', $response->getData()->Nombre);
        $this->assertEquals(100, $response->getData()->{'Puntos totales otorgados'});
        $this->assertEquals(80, $response->getData()->{'Puntos sin canjear'});
        $this->assertEquals('2024-03-01 00:00:00', $response->getData()->{'Fecha inicial'});
        $this->assertEquals('2024-03-31 23:59:59', $response->getData()->{'Fecha fin'});
    }

    public function testPuntosFarmaciaClienteConParametrosValidos()
    {
        // Configurar el mock para validarFarmacia y validarCliente
        $this->validadorApiServiceMock->shouldReceive('validarFarmacia')
            ->andReturn(Mockery::mock(Farmacia::class));
        $this->validadorApiServiceMock->shouldReceive('validarCliente')
            ->andReturn(Mockery::mock(Cliente::class));

        // Crear una instancia de Request con los parámetros válidos
        $request = new Request([
            'idFarmacia' => 1,
            'idCliente' => 1,
        ]);

        // Configurar el mock para consultaPuntosFarmaciaCliente
        $this->consultaPuntosServiceMock->shouldReceive('consultaPuntosFarmaciaCliente')
            ->andReturn([
                'nombre_farmacia' => 'Farmacia ABC',
                'nombre_cliente' => 'Cliente XYZ',
                'total_puntos' => 200,
            ]);

        // Ejecutar el método puntosFarmaciaCliente del controlador
        $response = $this->consultaPuntosController->puntosFarmaciaCliente($request);

        // Verificar la respuesta esperada
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $response->getData()->{'Id farmacia'});
        $this->assertEquals('Farmacia ABC', $response->getData()->{'Nombre farmacia'});
        $this->assertEquals(1, $response->getData()->{'Id cliente'});
        $this->assertEquals('Cliente XYZ', $response->getData()->{'Nombre cliente'});
        $this->assertEquals(200, $response->getData()->{'Puntos totales otorgados por la farmacia'});
    }

    public function testSaldoClienteConParametrosValidos()
    {
        // Configurar el mock para validarCliente
        $this->validadorApiServiceMock->shouldReceive('validarCliente')
            ->andReturn(Mockery::mock(Cliente::class));

        // Crear una instancia de Request con los parámetros válidos
        $request = new Request([
            'idCliente' => 1,
        ]);

        // Configurar el mock para consultaSaldoCliente
        $this->consultaPuntosServiceMock->shouldReceive('consultaSaldoCliente')
            ->andReturn([
                'nombre_cliente' => 'Cliente XYZ',
                'saldo_actual' => 200,
            ]);

        // Ejecutar el método saldoCliente del controlador
        $response = $this->consultaPuntosController->saldoCliente($request);

        // Verificar la respuesta esperada
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $response->getData()->{'Id cliente'});
        $this->assertEquals('Cliente XYZ', $response->getData()->{'Nombre cliente'});
        $this->assertEquals(200, $response->getData()->{'Saldo actual'});
    }
}




