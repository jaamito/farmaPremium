<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    
    public function testAcumular()
    {   
        //Test acumular puntos ok
        $data = [
            'idFarmacia' => 1,
            'idCliente' => 1,
            'puntos' => 5
        ];

        $response = $this->post('/api/acumular', $data);
        $response->assertStatus(200);

        //Test error al introducir tipo de valor incorrecto
        $data = [
            'idFarmacia' => "texto",
            'idCliente' => 1,
            'puntos' => 5
        ];

        $response = $this->post('/api/acumular', $data);
        $response->assertStatus(422);

        //Test falta de parametros en la llamada
        $data = [
            'idFarmacia' => "texto",
            'idCliente' => 1
        ];

        $response = $this->post('/api/acumular', $data);
        $response->assertStatus(422);

        //Test al no pasar un valor correcto
        $data = [
            'idFarmacia' => 9000,
            'idCliente' => 1,
            'puntos' => 5
        ];

        $response = $this->post('/api/acumular', $data);
        $response->assertStatus(401);

        //Test al no pasar un valor correcto
        $data = [
            'idFarmacia' => 1,
            'idCliente' => 9000,
            'puntos' => 5
        ];

        $response = $this->post('/api/acumular', $data);
        $response->assertStatus(401);

    }
    
    public function testCanjear()
    {   
        //Test canjear puntos ok
        $data = [
            'idFarmacia' => 1,
            'idCliente' => 1,
            'puntos' => 5
        ];

        $response = $this->post('/api/canjear', $data);
        $response->assertStatus(200);

        //Test error al introducir tipo de valor incorrecto
        $data = [
            'idFarmacia' => "texto",
            'idCliente' => 1,
            'puntos' => 5
        ];

        $response = $this->post('/api/canjear', $data);
        $response->assertStatus(422);

        //Test falta de parametros en la llamada
        $data = [
            'idFarmacia' => "texto",
            'idCliente' => 1
        ];

        $response = $this->post('/api/canjear', $data);
        $response->assertStatus(422);

        //Test al no pasar un valor correcto
        $data = [
            'idFarmacia' => 9000,
            'idCliente' => 1,
            'puntos' => 5
        ];

        $response = $this->post('/api/canjear', $data);
        $response->assertStatus(401);

        //Test al no pasar un valor correcto
        $data = [
            'idFarmacia' => 1,
            'idCliente' => 9000,
            'puntos' => 5
        ];

        $response = $this->post('/api/canjear', $data);
        $response->assertStatus(401);

    }
    
    public function testConsultaSaldoCliente(){

        //Test funcionamiento ok
        $response = $this->get('/api/consultar/saldo-cliente?idCliente=1');
        $response->assertStatus(200);

        //Test Id no existe
        $response = $this->get('/api/consultar/saldo-cliente?idCliente=1000');
        $response->assertStatus(401);

        //Test sin pasar parametro ID
        $response = $this->get('/api/consultar/saldo-cliente');
        $response->assertStatus(422);

    }

    public function testConsultaPuntosFarmaciaCliente(){

        $response = $this->get('/api/consultar/puntos-farmacia-cliente?idFarmacia=1&idCliente=1');
        $response->assertStatus(200);

        //Test Id no existe
        $response = $this->get('/api/consultar/puntos-farmacia-cliente?idFarmacia=1000&idCliente=1');
        $response->assertStatus(401);

        //Test sin pasar parametro IDcliente
        $response = $this->get('/api/consultar/puntos-farmacia-cliente?idFarmacia=1');
        $response->assertStatus(422);
    }

    public function testConsultaPeriodoFarmacia(){

        $response = $this->get('/api/consultar/puntos-periodo-farmacia?idFarmacia=3&dateFrom=2024-06-19 00:12:20&dateTo=2024-06-19 00:12:20');
        $response->assertStatus(200);

        //Test Id no existe
        $response = $this->get('/api/consultar/puntos-periodo-farmacia?idFarmacia=2000&dateFrom=2024-06-19 00:12:20&dateTo=2024-06-19 00:12:20');
        $response->assertStatus(401);

        //Test sin pasar parametro date
        $response = $this->get('/api/consultar/puntos-periodo-farmacia?idFarmacia=3&dateFrom=2024-06-19 00:12:20');
        $response->assertStatus(422);

    }
}
