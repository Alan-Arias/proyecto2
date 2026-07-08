<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$rolesPermitidos): Response
    {
        $usuario = $request->user();

        if (! $usuario || ! $usuario->activo || ! $usuario->tieneRol($rolesPermitidos)) {
            abort(403, 'No tiene permiso para acceder a esta opcion del sistema.');
        }

        return $next($request);
    }
}
