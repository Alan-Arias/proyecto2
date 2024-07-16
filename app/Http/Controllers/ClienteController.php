<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * env modificado v2
     * Display a listing of the resource.
     */
    public function index()
    {
        $Cliente = Cliente::all();
        $Usuarios = Usuarios::all();
        return view('cliente.frmcliente', compact('Cliente', 'Usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $Cliente = new Cliente();
        $Cliente->nombres = $request->nombres;
        $Cliente->edad = $request->edad;
        $Cliente->sexo = $request->sexo;
        $Cliente->usuario_id = $request->user;
        $Cliente->save();
        return back()->with('agregar', 'Se ha registrado Correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
