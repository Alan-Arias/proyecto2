<?php

namespace App\Http\Controllers;
use App\Models\Vehiculo;
use App\Models\Cliente;
use App\Models\Trabajador;
use App\Models\Servicio;
use App\Models\AsignacionServicio;
use App\Models\Reserva;
use App\Models\DetalleVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class VehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Detalle = DetalleVehiculo::with(['vehiculo', 'asignacion_servicio.servicio'])->get();
        $Vehiculo=Vehiculo::all();
        $Cliente=Cliente::all();
        $Trabajador=Trabajador::all();
        $Servicio=Servicio::all();
        $Reserva=Reserva::all();
        $AsignacionServicio=AsignacionServicio::all();
        return view('vehiculo.frmvehiculo',compact('Vehiculo','Cliente','Trabajador','Servicio','AsignacionServicio','Reserva', 'Detalle'));
    }
    public function index2()
    {
        $Vehiculo=Vehiculo::all();
        $Cliente=Cliente::all();
        $Trabajador=Trabajador::all();
        $Servicio=Servicio::all();
        $Reserva=Reserva::all();
        $AsignacionServicio=AsignacionServicio::all();
        return view('vehiculo.vehiculotabla',compact('Vehiculo','Cliente','Trabajador','Servicio','AsignacionServicio','Reserva'));
    }
    public function index3()
    {
        $Vehiculo=Vehiculo::all();
        $Cliente=Cliente::all();
        $Trabajador=Trabajador::all();
        $Servicio=Servicio::all();
        $Reserva=Reserva::all();
        $AsignacionServicio=AsignacionServicio::all();
        return view('vehiculo.regvehiculo',compact('Vehiculo','Cliente','Trabajador','Servicio','AsignacionServicio','Reserva'));
    }
    public function consultaDetVehic(Request $request)
    {
        // Recuperar los datos de marca y modelo desde el formulario
        $marcaVehiculo = $request->input('marcaVehiculo');
        $modeloVehiculo = $request->input('modeloVehiculo');

        // Construir la consulta SQL
        $detalles = DB::table('vehiculos AS v')
                        ->select('v.id AS vehiculo_id', 'v.marca', 'v.modelo', 'v.año', 'v.color', 'v.placa',
                                 'a.id AS asignacion_id', 'a.fecha_asignacion', 'a.estado')
                        ->join('detalle_vehiculos AS vd', 'v.id', '=', 'vd.vehiculo_id')
                        ->join('asignacion_servicios AS a', 'vd.asignacion_servicio_id', '=', 'a.id')
                        ->where('v.marca', 'LIKE', '%' . $marcaVehiculo . '%')
                        ->where('v.modelo', 'LIKE', '%' . $modeloVehiculo . '%')
                        ->get();

        // Devolver la vista con los detalles
        return view('vehiculo.consultaDetVehic', ['detalles' => $detalles]);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function guardarAsignacionServicio(Request $request)
    {
        $AsignacionServicio=new AsignacionServicio();
        $AsignacionServicio->fecha_asignacion=$request->fecha_asignacion;
        $AsignacionServicio->estado=$request->estado;
        $AsignacionServicio->id_trabajador=$request->trabajador;
        $AsignacionServicio->id_servicio=$request->servicio;
        $AsignacionServicio->save();


        return back()->with('agregar', 'Se ha registrado Correctamente');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $Vehiculo=new Vehiculo();
        $Vehiculo->id_cliente=$request->id;
        $Vehiculo->marca=$request->marca;
        $Vehiculo->modelo=$request->modelo;
        $Vehiculo->año=$request->año;
        $Vehiculo->color=$request->color;
        $Vehiculo->placa=$request->placa;
        $Vehiculo->save();

        $Reserva=new Reserva();
        //$Reserva->id_servicio=$request->id_servicio;
        $Reserva->fecha_reserva=$request->fecha_reserva;
        $Reserva->id_cliente=$request->id;
        $Reserva->detalle=$request->detalle;
        $Reserva->save();

        return back()->with('agregar', 'Se ha registrado Correctamente');
    }
    public function guardarVehiculos(Request $request)
    {
        $Vehiculo=new Vehiculo();
        $Vehiculo->id_cliente=$request->id;
        $Vehiculo->marca=$request->marca;
        $Vehiculo->modelo=$request->modelo;
        $Vehiculo->año=$request->año;
        $Vehiculo->color=$request->color;
        $Vehiculo->placa=$request->placa;
        $Vehiculo->save();
        return back()->with('agregar', 'Sus datos se han registrado correctamente en la Base de Datos');
    }
    /**
     * Display the specified resource.
     */

     public function RecolectarDatos(Request $request)
    {
        try {
            $lcComerceID           = "d029fa3a95e174a19934857f535eb9427d967218a36ea014b70ad704bc6c8d1c";
            $lnMoneda              = 2;
            $lnTelefono            = $request->tnTelefono;
            $lcNombreUsuario       = $request->tcRazonSocial;
            $lnCiNit               = $request->tcCiNit;
            $lcNroPago = rand(10000000, 99999999);
            $lnMontoClienteEmpresa = $request->tnMonto;
            $lcCorreo              = $request->tcCorreo;
            $lcUrlCallBack         = "http://localhost:8000/";
            $lcUrlReturn           = "http://localhost:8000/";
            $laPedidoDetalle       = $request->taPedidoDetalle;
            $lcUrl                 = "";

            $loClient = new Client();

            if ($request->tnTipoServicio == 1) {
                $lcUrl = "https://serviciostigomoney.pagofacil.com.bo/api/servicio/generarqrv2";
            } elseif ($request->tnTipoServicio == 2) {
                $lcUrl = "https://serviciostigomoney.pagofacil.com.bo/api/servicio/realizarpagotigomoneyv2";
            }

            $laHeader = [
                'Accept' => 'application/json'
            ];

            $laBody   = [
                "tcCommerceID"          => $lcComerceID,
                "tnMoneda"              => $lnMoneda,
                "tnTelefono"            => $lnTelefono,
                'tcNombreUsuario'       => $lcNombreUsuario,
                'tnCiNit'               => $lnCiNit,
                'tcNroPago'             => $lcNroPago,
                "tnMontoClienteEmpresa" => $lnMontoClienteEmpresa,
                "tcCorreo"              => $lcCorreo,
                'tcUrlCallBack'         => $lcUrlCallBack,
                "tcUrlReturn"           => $lcUrlReturn,
                'taPedidoDetalle'       => $laPedidoDetalle
            ];

            $loResponse = $loClient->post($lcUrl, [
                'headers' => $laHeader,
                'json' => $laBody
            ]);

            $laResult = json_decode($loResponse->getBody()->getContents());

            if ($request->tnTipoServicio == 1) {

                $laValues = explode(";", $laResult->values)[1];
           

                $laQrImage = "data:image/png;base64," . json_decode($laValues)->qrImage;
                echo '<img src="' . $laQrImage . '" alt="Imagen base64">';
            } elseif ($request->tnTipoServicio == 2) {

             
                
                $csrfToken = csrf_token();

                echo '<h5 class="text-center mb-4">' . $laResult->message . '</h5>';
                echo '<p class="blue-text">Transacción Generada: </p><p id="tnTransaccion" class="blue-text">'. $laResult->values . '</p><br>';
                echo '<iframe name="QrImage" style="width: 100%; height: 300px;"></iframe>';
                echo '<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>';

                echo '<script>
                        $(document).ready(function() {
                            function hacerSolicitudAjax(numero) {
                                // Agrega el token CSRF al objeto de datos
                                var data = { _token: "' . $csrfToken . '", tnTransaccion: numero };
                                
                                $.ajax({
                                    url: \'/consultar\',
                                    type: \'POST\',
                                    data: data,
                                    success: function(response) {
                                        var iframe = document.getElementsByName(\'QrImage\')[0];
                                        iframe.contentDocument.open();
                                        iframe.contentDocument.write(response.message);
                                        iframe.contentDocument.close();
                                    },
                                    error: function(error) {
                                        console.error(error);
                                    }
                                });
                            }

                            setInterval(function() {
                                hacerSolicitudAjax(' . $laResult->values . ');
                            }, 7000);
                        });
                    </script>';


            
            }
        } catch (\Throwable $th) {

            return $th->getMessage() . " - " . $th->getLine();
        }
    }

    public function ConsultarEstado(Request $request)
    {
        $lnTransaccion = $request->tnTransaccion;
        
        $loClientEstado = new Client();

        $lcUrlEstadoTransaccion = "https://serviciostigomoney.pagofacil.com.bo/api/servicio/consultartransaccion";

        $laHeaderEstadoTransaccion = [
            'Accept' => 'application/json'
        ];

        $laBodyEstadoTransaccion = [
            "TransaccionDePago" => $lnTransaccion
        ];

        $loEstadoTransaccion = $loClientEstado->post($lcUrlEstadoTransaccion, [
            'headers' => $laHeaderEstadoTransaccion,
            'json' => $laBodyEstadoTransaccion
        ]);

        $laResultEstadoTransaccion = json_decode($loEstadoTransaccion->getBody()->getContents());

        $texto = '<h5 class="text-center mb-4">Estado Transacción: ' . $laResultEstadoTransaccion->values->messageEstado . '</h5><br>';

        return response()->json(['message' => $texto]);
    }

    public function urlCallback(Request $request)
    {
        $Venta = $request->input("PedidoID");
        $Fecha = $request->input("Fecha");
        $NuevaFecha = date("Y-m-d", strtotime($Fecha));
        $Hora = $request->input("Hora");
        $MetodoPago = $request->input("MetodoPago");
        $Estado = $request->input("Estado");
        $Ingreso = true;

        try {
          //  propceso de verificacion y procesando el pago ya en el lado del comercio
            $arreglo = ['error' => 0, 'status' => 1, 'message' => "Pago realizado correctamente.", 'values' => true];
        } catch (\Throwable $th) {
            $arreglo = ['error' => 1, 'status' => 1, 'messageSistema' => "[TRY/CATCH] " . $th->getMessage(), 'message' => "No se pudo realizar el pago, por favor intente de nuevo.", 'values' => false];
        }

        return response()->json($arreglo);
    }

    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
