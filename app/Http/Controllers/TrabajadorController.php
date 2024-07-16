<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\Models\Usuarios;
use App\Models\Trabajador;
use Illuminate\Http\Request;

class TrabajadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Trabajador = DB::table('trabajadors')
            ->join('usuarios', 'trabajadors.usuario_id', '=', 'usuarios.id')
            ->select('trabajadors.*', 'usuarios.user', 'usuarios.password', 'usuarios.desc_user')
            ->get();
        return view('trabajador.frmtrabajador', compact('Trabajador'));
    }
    
    

    public function index2()
    {
        $Trabajador=Trabajador::all();
        $Usuarios=Usuarios::all();
        return view('trabajador.trabajadortabla',compact('Trabajador', 'Usuarios'));
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
        $Usuarios = new Usuarios();
        $Usuarios->id = $request->id;
        $Usuarios->user = $request->user;
        $Usuarios->password = $request->pass;
        $Usuarios->tipo_user = $request->tipo_user;
        $Usuarios->desc_user = $request->desc_user;
        $Usuarios->save();

        $Trabajador=new Trabajador();
        $Trabajador->nombres=$request->nombres;
        $Trabajador->edad=$request->edad;
        $Trabajador->sexo=$request->sexo;
        $Trabajador->usuario_id=$request->id;
        $Trabajador->save();
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
