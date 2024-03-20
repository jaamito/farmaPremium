<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AcumularPuntosServiceInterface;
use App\Services\ValidarDatosServiceInterface;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="API de Puntos de Fidelización",
 *     version="1.0.0",
 *     description="API para gestionar los puntos de fidelización de una tarjeta de cliente.",
 *     @OA\Contact(
 *         email="gm.2012.ian.lopez@gmail.com",
 *         name="Ian lopez Zamora"
 *     ),
 *     @OA\License(
 *         name="",
 *         url=""
 *     )
 * )
 */

class PuntosController extends Controller
{
    private $acumularPuntosService;
    private $validadorApiService;


    public function __construct(AcumularPuntosServiceInterface $acumularPuntosService, ValidarDatosServiceInterface $validadorApiService)
    {
        $this->acumularPuntosService = $acumularPuntosService;
        $this->validadorApiService = $validadorApiService;
    }    

    /**
 * @OA\Post(
 *     path="/api/acumular",
 *     tags={"Acumular"},
 *     summary="Acumular puntos",
 *     description="Permite acumular puntos para un cliente en una farmacia.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"idFarmacia", "idCliente", "puntos"},
 *             @OA\Property(property="idFarmacia", type="integer", example="1"),
 *             @OA\Property(property="idCliente", type="integer", example="1"),
 *             @OA\Property(property="puntos", type="integer", example="10")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Puntos acumulados correctamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="object",
 *                 @OA\Property(property="Status", type="integer", example=200),
 *                 @OA\Property(property="Mensaje", type="string", example="Puntos acumulados correctamente")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Datos de entrada no válidos",
 *         @OA\JsonContent(
 *             @OA\Property(property="Status", type="integer", example=422),
 *             @OA\Property(property="Mensaje", type="object",
 *                 @OA\Property(property="idFarmacia", type="array",
 *                     @OA\Items(type="string", example="the id farmacia field is required", description="The id farmacia field must be an integer o the id farmacia field is required")
 *                 ),
 *                 @OA\Property(property="idCliente", type="array",
 *                     @OA\Items(type="string", example="the id cliente field is required", description="The id cliente field must be an integer o the id cliente field is required")
 *                 ),
 *                 @OA\Property(property="puntos", type="array",
 *                     @OA\Items(type="string", example="the puntos field is required", description="The id puntos field must be an integer o the id puntos field is required")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *      response=401,
 *      description="Error de autenticación",
 *      @OA\JsonContent(
 *         @OA\Property(property="Status", type="integer", example=401),
 *         @OA\Property(property="Mensaje", type="string", example="El id de farmacia no existe", description="ID de farmacia o cliente no existe")
 *     )
 * ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="Status", type="integer", example=500),
 *             @OA\Property(property="Mensaje", type="string", example="Error interno del servidor, contacta con el administrador de la API")
 *         )
 *     )
 * )
 */

    public function acumular(Request $request)
    {   
        try{
            //Verificación parametros recibidos en la llamada
            $validator = Validator::make($request->all(),[
                'idFarmacia' => 'required|integer',
                'idCliente' => 'required|integer',
                'puntos' => 'required|integer',
            ]);

            if($validator->fails())
            {
                return new JsonResponse([
                    'Status' => 422,
                    'Mensaje' => $validator->errors()
                ], 422);
            }
            
            $idFarmacia = $request->input('idFarmacia');
            $idCliente = $request->input('idCliente');
            $puntos = $request->input('puntos');

            //Controlar posibles errores
            $existeFarmacia = $this->validadorApiService->validarFarmacia($idFarmacia);
            if (!$existeFarmacia) 
            {
                return new JsonResponse([
                    'Status' => 401,
                    'Mensaje' => "El id ".$idFarmacia." de farmacia no existe."
                ], 401);
            }
            $existeCliente  = $this->validadorApiService->validarCliente($idCliente);
            if (!$existeCliente) 
            {
                return new JsonResponse([
                    'Status' => 401,
                    'Mensaje' => "El id ".$idCliente." de cliente no existe."
                ], 401);
            }

            //Ejecutar lógica de la llamada
            $this->acumularPuntosService->acumularPuntos($idFarmacia, $idCliente, $puntos);
            
            return new JsonResponse([
                'Status' => 200,
                'Mensaje' => 'Puntos acumulados correctamente'
            ], 200);
            
        }catch (\Exception $e){

            //con $e->message(); podríamos enviar un mail al dev para revisar el error interno.
            
            var_dump($e->getMessage());
            return new JsonResponse([
                'Status' => 500,
                'Mensaje' => 'Error interno del servidor, contacta con el administrador de la API.'
            ], 500);
        }
    }
}
