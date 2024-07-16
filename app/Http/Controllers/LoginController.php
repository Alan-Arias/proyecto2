<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuarios; // Asegúrate de importar el modelo correcto
use Illuminate\Support\Facades\DB; 

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view(url('login.login'));
    }

    public function login(Request $request)
{
    $credentials = $request->only('user', 'password');

    // Verificar si el usuario y la contraseña introducidos existen en la tabla usuarios
    $user = Usuarios::where('user', $credentials['user'])->first();

    if (!$user || $user->password != $credentials['password']) {
        // Usuario y contraseña no encontrados en la tabla usuarios
        return redirect(url('/login'))
            ->with(['error' => 'Usuario o contraseña incorrecto']);
    }

    // Obtener el campo "nombres" e "id" de la tabla "clientes" utilizando un inner join
    $cliente = DB::table('clientes')
        ->join('usuarios', 'clientes.usuario_id', '=', 'usuarios.id')
        ->where('usuarios.id', $user->id)
        ->select('clientes.id', 'clientes.nombres') // Incluir el campo 'id' de clientes
        ->first();

    // Obtener el campo "nombres" e "id" de la tabla "trabajadors" utilizando un inner join
    $trabajador = DB::table('trabajadors')
        ->join('usuarios', 'trabajadors.usuario_id', '=', 'usuarios.id')
        ->where('usuarios.id', $user->id)
        ->select('trabajadors.id', 'trabajadors.nombres') // Incluir el campo 'id' de trabajadors
        ->first();

    // Guardar el nombre y el id del cliente o trabajador en la sesión según el tipo de usuario
    if ($user->desc_user == 'cliente' && $cliente) {
        session(['nombres' => $cliente->nombres, 'cliente_id' => $cliente->id]);
    } elseif ($user->desc_user == 'trabajador' && $trabajador) {
        session(['nombres' => $trabajador->nombres, 'trabajador_id' => $trabajador->id]);
    }

    // Verificar el tipo de usuario y redirigir en consecuencia
    if ($user->desc_user == 'trabajador') {
        // Usuario es un trabajador
        return redirect(url('/trabajadorindex'));
    } else if ($user->desc_user == 'cliente') {
        // Usuario es un cliente
        return redirect(url('/clientelog'));
    } else if ($user->desc_user == 'admin') {
        // Usuario es un administrador
        return redirect(url('/admin'));
    }
}
public function logout(Request $request)
    {
        Auth::logout(); // Cerrar sesión del usuario
        $request->session()->invalidate(); // Invalidar la sesión existente
        $request->session()->regenerateToken(); // Generar un nuevo token CSRF

        return redirect(url('/')); // Redirigir al usuario a la página principal
    }

}
