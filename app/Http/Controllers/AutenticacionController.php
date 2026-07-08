<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AutenticacionController extends Controller
{
    public function mostrarLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function iniciarSesion(LoginRequest $request): RedirectResponse
    {
        $credenciales = $request->only('email', 'password');

        if (! Auth::attempt($credenciales, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ])->onlyInput('email');
        }

        if (! Auth::user()->activo) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'El usuario se encuentra desactivado.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('exito', 'Bienvenido al sistema.');
    }

    public function cerrarSesion(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio')->with('exito', 'Sesion cerrada correctamente.');
    }
}
