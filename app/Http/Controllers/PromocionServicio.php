<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\Servicio;
use Illuminate\Http\Request;

class PromocionServicio extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $PromocionServicio=Promocion::all();
        
        return view('promocion.frmpromocion',compact('PromocionServicio'));
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
        $PromocionServicio=new Promocion();
        $PromocionServicio->descripcion=$request->descripcion;
        $PromocionServicio->descuento=$request->descuento;
        $PromocionServicio->estado=$request->estado;
        $PromocionServicio->fecha_inicio=$request->fecha_inicio;
        $PromocionServicio->fecha_fin=$request->fecha_fin;
        $PromocionServicio->save();
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
