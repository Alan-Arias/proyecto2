<?php

namespace App\Http\Controllers;

use App\Models\DetalleVehiculo;
use App\Models\AsignacionServicio;
use App\Models\Vehiculo;
use App\Models\Servicio;
use App\Models\Trabajador;
use Illuminate\Http\Request;

class VehiculoDetalleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Detalle = DetalleVehiculo::with(['vehiculo', 'asignacion_servicio.servicio'])->get();
        $Vehiculo = Vehiculo::all();
        $Servicio = Servicio::all();
        $Trabajador = Trabajador::all();
        $AsignacionServicio = AsignacionServicio::with('servicio')->get();
        return view('detalleservicio.frmdetalleserviciosauto', compact('Detalle', 'Vehiculo', 'Servicio', 'AsignacionServicio', 'Trabajador'));
    }
    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validar los datos del formulario
    $validatedData = $request->validate([
        'vehiculo_ids' => 'required|array',
        'vehiculo_ids.*' => 'exists:vehiculos,id',
        'asignacion_servicio_ids' => 'required|array',
        'asignacion_servicio_ids.*' => 'exists:asignacion_servicios,id',
        'fechas_servicio' => 'required|array',
        'fechas_servicio.*' => 'date',
    ]);

    // Crear los nuevos registros en la tabla detalle_vehiculos
    foreach ($request->vehiculo_ids as $index => $vehiculo_id) {
        DetalleVehiculo::create([
            'vehiculo_id' => $vehiculo_id,
            'asignacion_servicio_id' => $request->asignacion_servicio_ids[$index],
            'fecha_servicio' => $request->fechas_servicio[$index],
        ]);
    }
    return redirect()->back()->with('agregar', 'Detalles registrados exitosamente.');
}

    /**
     * Display the specified resource.
     */
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
