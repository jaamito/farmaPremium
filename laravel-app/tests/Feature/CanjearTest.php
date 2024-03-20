<?php 

use App\Http\Controllers\CanjearPuntosController;
use App\Services\CanjearPuntosServiceInterface;
use App\Services\ValidarDatosServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Models\Farmacia;
use App\Models\Cliente;

class CanjearTest extends TestCase
{
    protected $canjearPuntosServiceMock;
    protected $validadorApiServiceMock;
    protected $canjearPuntosController;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear mocks para los servicios
        $this->canjearPuntosServiceMock = Mockery::mock(CanjearPuntosServiceInterface::class);
        $this->validadorApiServiceMock = Mockery::mock(ValidarDatosServiceInterface::class);

        // Crear una instancia del controlador con los mocks de servicios
        $this->canjearPuntosController = new CanjearPuntosController(
            $this->canjearPuntosServiceMock,
            $this->validadorApiServiceMock
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testCanjearConParametrosValidos()
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


        $this->canjearPuntosServiceMock->shouldReceive('canjearPuntos')->andReturn([
            'status' => 200,
            'message' => 'Puntos canjeados correctamente'
        ]);

        // Crear una instancia de Request con los parámetros válidos
        $request = Request::create('/api/canjear', 'POST', [
            'idFarmacia' => 2,
            'idCliente' => 1,
            'puntos' => 1,
        ]);

        // Ejecutar el método canjear del controlador
        $response = $this->canjearPuntosController->canjear($request);

        // Verificar la respuesta esperada
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Puntos canjeados correctamente', $response->getData()->Mensaje);
    }

}

