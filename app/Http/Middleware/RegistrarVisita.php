<?php

namespace App\Http\Middleware;

use App\Models\VisitaPagina;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrarVisita
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && ! $request->expectsJson()) {
            try {
                $nombrePagina = $request->route()?->getName() ?? $request->path();

                VisitaPagina::query()->updateOrCreate(
                    ['pagina' => $nombrePagina],
                    ['visitas' => VisitaPagina::where('pagina', $nombrePagina)->value('visitas') + 1]
                );
            } catch (\Throwable) {
                // Permite abrir el sistema aunque las migraciones todavia no se hayan ejecutado.
            }
        }

        return $next($request);
    }
}
