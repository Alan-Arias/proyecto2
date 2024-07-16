<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Trabajador;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Servicio=Servicio::all();
        $Trabajador=Trabajador::all();
        return view('servicio.frmservicio',compact('Servicio','Trabajador'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $Servicio=new Servicio();
        $Servicio->nombre=$request->nombres;
        $Servicio->descripcion=$request->descripcion;
        $Servicio->costo=$request->costo;
        $Servicio->duracion_estimada=$request->duracion_estimada;
        $Servicio->estado=$request->estado;
        $Servicio->id_trabajador=$request->id_trabajador;
        $Servicio->save();
        return back()->with('agregar', 'Se ha registrado Correctamente');
    }

    public function edit($id)
    {
        $Servicios= Servicio::findOrFail($id);
        $Servicio= Servicio::all();
        return view('servicio.frmservicio',compact('Servicio','Servicios'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
{
    $Servicio = Servicio::findOrFail($request->id);
    $Servicio->nombre = $request->nombre;
    $Servicio->descripcion = $request->descripcion;
    $Servicio->estado = $request->estado;
    $Servicio->save();
    $mensaje = 'Se ha actualizado correctamente';
    return redirect('/servicios')->with(compact('mensaje'));
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
