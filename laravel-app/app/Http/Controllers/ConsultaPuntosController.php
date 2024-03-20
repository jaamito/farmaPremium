<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ConsultaPuntosServiceInterface;
use App\Services\ValidarDatosServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ConsultaPuntosController extends Controller
{
    private $consultaPuntosService;
    private $validadorApiService;

    public function __construct(ConsultaPuntosServiceInterface $consultaPuntosService, ValidarDatosServiceInterface $validadorApiService)
    {
        $this->consultaPuntosService = $consultaPuntosService;
        $this->validadorApiService = $validadorApiService;
    }    

    /**
     * @OA\Get(
     *     path="/api/consultar/puntos-periodo-farmacia",
     *     summary="Puntos totales otorgados por la farmacia en un periodo de tiempo",
     *     tags={"Consultar"},
     *     @OA\Parameter(
     *         name="idFarmacia",
     *         in="query",
     *         required=true,
     *         description="ID Farmacia",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="dateFrom",
     *         in="query",
     *         required=true,
     *         description="Fecha inicial en formato YYYY-MM-DD HH:MM:SS",
     *         @OA\Schema(
     *             type="string",
     *             format="date-time"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="dateTo",
     *         in="query",
     *         required=true,
     *         description="Fecha final en formato YYYY-MM-DD HH:MM:SS",
     *         @OA\Schema(
     *             type="string",
     *             format="date-time"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Saldo consultado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="Status", type="integer", example=200),
     *             @OA\Property(property="Id", type="integer", example=1),
     *             @OA\Property(property="Nombre", type="string", example="Farmacia A"),
     *             @OA\Property(property="Puntos totales otorgados", type="integer"),
     *             @OA\Property(property="Puntos sin canjear", type="integer"),
     *             @OA\Property(property="Fecha inicial", type="string", format="date-time"),
     *             @OA\Property(property="Fecha fin", type="string", format="date-time")
     *         )
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Datos de entrada no válidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="Status", type="integer", example=422),
     *             @OA\Property(property="Mensaje", type="object",
     *                 @OA\Property(property="idFarmacia", type="array",
     *                     @OA\Items(type="string", example="the id farmacia field is required" , description="The id farmacia field must be an integer o the id farmacia field is required")
     *                 ),
     *                 @OA\Property(property="dateFrom", type="array",
     *                     @OA\Items(type="string", example="The date from field must match the format Y-m-d H:i:s." , description="The date from field must match the format Y-m-d H:i:s. o the id cliente field is required")
     *                 ),
     *                 @OA\Property(property="dateTo", type="array",
     *                     @OA\Items(type="string", example="The date to field must match the format Y-m-d H:i:s." , description="The date to field must match the format Y-m-d H:i:s. o the id puntos field is required")
     *                 )
     *             )
     *         )
     *     ),
     *  @OA\Response(
     *      response=401,
     *      description="Error de autenticación",
     *      @OA\JsonContent(
     *         @OA\Property(property="Status", type="integer", example=401),
     *         @OA\Property(property="Mensaje", type="string", example="El id de farmacia no existe", description="ID de farmacia no existe")
     *     )
     * ),@OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="Status", type="integer", example=500),
     *             @OA\Property(property="Mensaje", type="string", example="Error interno del servidor, contacta con el administrador de la API")
     *         )
     *     )
     * )
     * )
     */

    public function puntosPeriodoFarmacia(Request $request)
    {   
        try{
            //Verificación parametros recibidos en la llamada
            $validator = Validator::make($request->all(), [
                'idFarmacia' => 'required|integer',
                'dateFrom' => ['required', 'date', 'date_format:Y-m-d H:i:s'],
                'dateTo' => ['required', 'date', 'date_format:Y-m-d H:i:s'],
            ]);

            if($validator->fails())
            {
                return new JsonResponse([
                    'Status' => 422,
                    'message' => $validator->errors()
                ], 422);
            }
            
            $idFarmacia = $request->input('idFarmacia');
            $dateFrom = $request->input('dateFrom');
            $dateTo = $request->input('dateTo');

            //Controlar posibles errores
            $existeFarmacia = $this->validadorApiService->validarFarmacia($idFarmacia);
            if (!$existeFarmacia) 
            {
                return new JsonResponse([
                    'Status' => 401,
                    'message' => "El id ".$idFarmacia." de farmacia no existe."
                ], 401);
            }

            //Ejecutar lógica de la llamada
            $datosFarmacia = $this->consultaPuntosService->consultaPuntosPeriodoFarmacia($idFarmacia, $dateFrom, $dateTo);

            if(!$datosFarmacia)
            {
                return new JsonResponse([
                    'Status' => 400,
                    'message' => "El id de farmacia ".$idFarmacia." no tiene puntos otorgados."
                ], 400);
            }

            return new JsonResponse([
                'Status' => 200,
                'Id' => $idFarmacia,
                'Nombre' => $datosFarmacia["nombre"],
                'Puntos totales otorgados' => $datosFarmacia["PuntosOtorgados"],
                'Puntos sin canjear' => $datosFarmacia["PuntosNoCanjeados"],
                'Fecha inicial' => $dateFrom,
                'Fecha fin' => $dateTo
            ], 200);
        }catch (\Exception $e){

            //con $e->message(); podríamos enviar un mail al dev para revisar el error interno.

            return new JsonResponse([
                'Status' => 500,
                'message' => 'Error interno del servidor, contacta con el administrador de la API.'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/consultar/puntos-farmacia-cliente",
     *     summary="Puntos totales otorgados por la farmacia a un cliente",
     *     tags={"Consultar"},
     *     @OA\Parameter(
     *         name="idCliente",
     *         in="query",
     *         required=true,
     *         description="ID del cliente",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="idFarmacia",
     *         in="query",
     *         required=true,
     *         description="ID de la farmacia",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Puntos consultados correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="Status", type="integer", example=200),
     *             @OA\Property(property="Id farmacia", type="integer", example=1),
     *             @OA\Property(property="Nombre farmacia", type="string", example="Farmacia A"),
     *             @OA\Property(property="Id cliente", type="integer", example= 1),
     *             @OA\Property(property="Nombre cliente", type="string" , example="Cliente 1"),
     *             @OA\Property(property="Puntos totales otorgados por la farmacia", type="integer")
     *         )
     *     ),
     *      @OA\Response(
     *      response=401,
     *      description="Error de autenticación",
     *      @OA\JsonContent(
     *         @OA\Property(property="Status", type="integer", example=401),
     *         @OA\Property(property="Mensaje", type="string", example="El id de farmacia no existe", description="ID de farmacia no existe o el ID cliente no existe")
     *     )
     * ),
     * @OA\Response(
     *         response=422,
     *         description="Datos de entrada no válidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="Status", type="integer", example=422),
     *             @OA\Property(property="Mensaje", type="object",
     *                 @OA\Property(property="idCliente", type="array",
     *                     @OA\Items(type="string", example="the id cliente field is required" , description="The id cliente field must be an integer o the id cliente field is required")
     *                 ),
     *                 @OA\Property(property="idFarmacia", type="array",
     *                     @OA\Items(type="string", example="the id farmacia field is required" , description="The id farmacia field must be an integer o the id farmacia field is required")
     *                 )
     *             )
     *         )
     *     ),
     * @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="Status", type="integer", example=500),
     *             @OA\Property(property="Mensaje", type="string", example="Error interno del servidor, contacta con el administrador de la API")
     *         )
     *     )
     * )
     * 
     * )
     */

    public function puntosFarmaciaCliente(Request $request)
    {   
        try{
            //Verificación parametros recibidos en la llamada
            $validator = Validator::make($request->all(), [
                'idFarmacia' => 'required|integer',
                'idCliente' => 'required|integer',
            ]);

            if($validator->fails())
            {
                return new JsonResponse([
                    'Status' => 422,
                    'message' => $validator->errors()
                ], 422);
            }
            
            $idFarmacia = $request->input('idFarmacia');
            $idCliente = $request->input('idCliente');
            
            //Controlar posibles errores
            $existeFarmacia = $this->validadorApiService->validarFarmacia($idFarmacia);
            if (!$existeFarmacia) 
            {
                return new JsonResponse([
                    'Status' => 401,
                    'message' => "El id ".$idFarmacia." de farmacia no existe."
                ], 401);
            }

            $existeCliente  = $this->validadorApiService->validarCliente($idCliente);
            if (!$existeCliente) 
            {
                return new JsonResponse([
                    'Status' => 401,
                    'message' => "El id ".$idCliente." de cliente no existe."
                ], 401);
            }

            //Ejecutar lógica de la llamada
            $datosPuntosFarmaciaCliente = $this->consultaPuntosService->consultaPuntosFarmaciaCliente($idFarmacia, $idCliente);

            if($datosPuntosFarmaciaCliente == [])
            {
                return new JsonResponse([
                    'Status' => 400,
                    'message' => "No existen puntos acumulados de este cliente."
                ], 400);
            }

            return new JsonResponse([
                'Status' => 200,
                'Id farmacia' => $idFarmacia,
                'Nombre farmacia' => $datosPuntosFarmaciaCliente["nombre_farmacia"],
                'Id cliente' => $idCliente,
                'Nombre cliente' => $datosPuntosFarmaciaCliente["nombre_cliente"],
                'Puntos totales otorgados por la farmacia' => $datosPuntosFarmaciaCliente["total_puntos"]
            ], 200);
        }catch (\Exception $e){

            //con $e->message(); podríamos enviar un mail al dev para revisar el error interno.

            return new JsonResponse([
                'Status' => 500,
                'message' => 'Error interno del servidor, contacta con el administrador de la API.'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/consultar/saldo-cliente",
     *     summary="Consultar saldo del cliente",
     *     tags={"Consultar"},
     *     @OA\Parameter(
     *         name="idCliente",
     *         in="query",
     *         required=true,
     *         description="ID del cliente",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Saldo consultado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="Status", type="integer", example=200),
     *             @OA\Property(property="Id cliente", type="integer", example=1),
     *             @OA\Property(property="Nombre cliente", type="string", example="Cliente 1"),
     *             @OA\Property(property="Saldo actual", type="integer", example=12)
     *         )
     *     ),
     * @OA\Response(
     *      response=401,
     *      description="Error de autenticación",
     *      @OA\JsonContent(
     *         @OA\Property(property="Status", type="integer", example=401),
     *         @OA\Property(property="Mensaje", type="string", example="El id de cliente no existe", description="ID de cliente no existe")
     *     )
     * ),
     * @OA\Response(
     *         response=422,
     *         description="Datos de entrada no válidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="Status", type="integer", example=422),
     *             @OA\Property(property="Mensaje", type="object",
     *                 @OA\Property(property="idCliente", type="array",
     *                     @OA\Items(type="string", example="the id cliente field is required" , description="The id cliente field must be an integer o the id cliente field is required")
     *                 )
     *             )
     *         )
     *     ),
     * @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="Status", type="integer", example=500),
     *             @OA\Property(property="Mensaje", type="string", example="Error interno del servidor, contacta con el administrador de la API")
     *         )
     *     )
     * )
     * )
     */

    public function saldoCliente(Request $request)
    {
        try{
            //Verificación parametros recibidos en la llamada
            $validator = Validator::make($request->all(), [
                'idCliente' => 'required|integer'
            ]);

            if($validator->fails())
            {
                return new JsonResponse([
                    'Status' => 422,
                    'message' => $validator->errors()
                ], 422);
            }
            
            $idCliente = $request->input('idCliente');

            //Controlar posibles errores
            $existeCliente  = $this->validadorApiService->validarCliente($idCliente);
            if (!$existeCliente) 
            {
                return new JsonResponse([
                    'Status' => 401,
                    'message' => "El id ".$idCliente." de cliente no existe."
                ], 401);
            }

            //Ejecutar lógica de la llamada
            $datosCliente = $this->consultaPuntosService->consultaSaldoCliente($idCliente);

            if($datosCliente == [])
            {
                return new JsonResponse([
                    'Status' => 400,
                    'message' => "Este cliente no tiene tarjeta de fidelización."
                ], 400);
            }

            return new JsonResponse([
                'Status' => 200,
                'Id cliente' => $idCliente,
                'Nombre cliente' => $datosCliente["nombre_cliente"],
                'Saldo actual' => $datosCliente["saldo_actual"]
            ], 200);
        }catch (\Exception $e){

            //con $e->message(); podríamos enviar un mail al dev para revisar el error interno.

            return new JsonResponse([
                'Status' => 500,
                'message' => 'Error interno del servidor, contacta con el administrador de la API.'
            ], 500);
        }
    }


}
