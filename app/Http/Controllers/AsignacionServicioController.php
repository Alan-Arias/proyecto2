<?php

namespace App\Http\Controllers;

use App\Models\AsignacionServicio;
use App\Models\Trabajador;
use App\Models\Servicio;
use Illuminate\Http\Request;

class AsignacionServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $AsignacionServicio=AsignacionServicio::all();
        $Trabajador=Trabajador::all();
        $Servicio=Servicio::all();
        return view('asignacion.frmasignacion',compact('AsignacionServicio','Servicio', 'Trabajador'));
    }
    public function index2()
    {
        $AsignacionServicio=AsignacionServicio::all();
        $Trabajador=Trabajador::all();
        $Servicio=Servicio::all();
        return view('asignacion.asignacionserviciotabla',compact('AsignacionServicio','Servicio', 'Trabajador'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function RegistrarAsignarServicio(Request $request)
    {
        $AsigServ=new AsignacionServicio();
        $AsigServ->fecha_asignacion=$request->fecha_servicio;
        $AsigServ->estado=$request->estado;
        $AsigServ->id_trabajador=$request->id_trabajador;
        $AsigServ->id_servicio=$request->servicio_id;
        $AsigServ->save();
        return back()->with('agregar', 'Se ha registrado Correctamente');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
