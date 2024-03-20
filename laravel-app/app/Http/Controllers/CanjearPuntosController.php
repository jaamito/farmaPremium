<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CanjearPuntosServiceInterface;
use App\Services\ValidarDatosServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class CanjearPuntosController extends Controller
{
    private $canjearPuntosService;

    public function __construct(CanjearPuntosServiceInterface $canjearPuntosService,ValidarDatosServiceInterface $validadorApiService)
    {
        $this->canjearPuntosService = $canjearPuntosService;
        $this->validadorApiService = $validadorApiService;
    }

    /**
     * @OA\Post(
     *     path="/api/canjear",
     *     tags={"Canjear"},
     *     summary="Canjear puntos",
     *     description="Permite canjear puntos para un cliente en una farmacia.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idFarmacia", "idCliente", "puntos"},
     *             @OA\Property(property="idFarmacia", type="integer", example="1"),
     *             @OA\Property(property="idCliente", type="integer", example="1"),
     *             @OA\Property(property="puntos", type="integer", example="5")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Puntos canjeados correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="object",
     *                 @OA\Property(property="Status", type="integer", example=200),
     *                 @OA\Property(property="Mensaje", type="string", example="Puntos canjeados correctamente")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No tienes suficientes puntos para canjear",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="object",
     *                 @OA\Property(property="Status", type="integer", example=400),
     *                 @OA\Property(property="Mensaje", type="string", example="No tienes suficientes puntos para canjear")
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
     *                     @OA\Items(type="string", example="the id cliente field is required", description="The id farmacia field must be an integer o the id farmacia field is required")
     *                 ),
     *                 @OA\Property(property="puntos", type="array",
     *                     @OA\Items(type="string", example="the puntos field is required", description="The id farmacia field must be an integer o the id farmacia field is required")
     *                 )
     *             )
     *         )
     *     ),
     * @OA\Response(
     *     response=401,
     *     description="Error de autenticación",
     *     @OA\JsonContent(
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

    public function canjear(Request $request)
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
                    'Mensaje' => "El id ".$idFarmacia." de farmacia no existe"
                ], 401);
            }
            $existeCliente  = $this->validadorApiService->validarCliente($idCliente);
            if (!$existeCliente) 
            {
                return new JsonResponse([
                    'Status' => 401,
                    'Mensaje' => "El id ".$idCliente." de cliente no existe"
                ], 401);
            }

            //Ejecutar lógica de la llamada
            $response = $this->canjearPuntosService->canjearPuntos($idFarmacia, $idCliente, $puntos);
            
            return new JsonResponse([
                'Status' => $response["status"],
                'Mensaje' => $response["message"]
            ], $response["status"]);

        }catch (\Exception $e){

            var_dump($e->getMessage());
            //con $e->getMessage(); podríamos enviar un mail al dev para revisar el error interno.
            return  new JsonResponse([
                'Status' => 500,
                'Mensaje' => 'Error interno del servidor, contacta con el administrador de la API'
            ], 500);
            
        }
    }
}
