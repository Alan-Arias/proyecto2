<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios;
use App\Models\Cliente;

class UsuarioController extends Controller
{
    public function index(Request $request)
{
    $criterio = $request->criterio;
    $buscar = $request->buscar;
    $Clientes = Cliente::all();

    if ($buscar == '') {
        $Usuarios = Usuarios::all();
    } else {
        $Usuarios = Usuarios::where($criterio, 'like', '%' . $buscar . '%')->get();
    }

    return view('user.frmuser', compact('Usuarios','Clientes'));
}


    public function store(Request $request)
    {
        $Usuarios = new Usuarios();
        $Usuarios->id = $request->id;
        $Usuarios->user = $request->user;
        $Usuarios->password = $request->pass;
        $Usuarios->tipo_user = $request->tipo_user;
        $Usuarios->desc_user = $request->desc_user;
        $Usuarios->save();

        $Cliente = new Cliente();
        $Cliente->nombres = $request->nombres;
        $Cliente->edad = $request->edad;
        $Cliente->sexo = $request->sexo;
        $Cliente->usuario_id = $request->id;
        $Cliente->save();
        
        return back()->with('agregar', 'Se ha registrado Correctamente');
    }
    public function destroy($id)
    {
        $Usuarios = Usuarios::find($id);
        $Usuarios->delete();
        return back()->with('eliminar', 'Se ha eliminado Correctamente');
    }
}

