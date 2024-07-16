<?php

namespace App\Http\Controllers;
use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Servicio;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $clientes = Cliente::all(); 
    $servicios = Servicio::all(); 
    $Reserva = Reserva::all();

    return view('reserva.frmreserva', compact('clientes', 'servicios','Reserva'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $Reserva=new Reserva();
        $Reserva->id_servicio=$request->id_servicio;
        $Reserva->fecha_reserva=$request->fecha_reserva;
        $Reserva->id_cliente=$request->id_cliente;
        $Reserva->detalle=$request->detalle;
        $Reserva->save();
        return back()->with('agregar', 'Se ha registrado Correctamente');
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
        $Reservas = Reserva::with('servicio', 'cliente')->findOrFail($id);
        $Reserva = Reserva::with('servicio', 'cliente')->get(); // Fetch all reservations with related service and client to display in the table
        $Servicios = Servicio::all();
        return view('reserva.frmreserva', compact('Reserva', 'Reservas', 'Servicios'));
    }
    
    public function update(Request $request, string $id)
    {
        $Reserva = Reserva::findOrFail($id);
        $Reserva->id_servicio = $request->id_servicio;
        $Reserva->detalle = $request->detalle;
        $Reserva->save();
        $mensaje = 'Se ha actualizado correctamente';
        return redirect('/reserva')->with(compact('mensaje'));
    }
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
