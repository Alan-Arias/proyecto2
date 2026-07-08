<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use App\Models\VisitaPagina;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => fn () => [
                'usuario' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'roles' => $request->user()->roles->pluck('nombre')->values(),
                ] : null,
            ],
            'menus' => fn () => $this->obtenerMenusDelUsuario($request),
            'visitasFooter' => fn () => $this->obtenerTotalVisitas(),
            'flash' => fn () => [
                'exito' => $request->session()->get('exito'),
                'error' => $request->session()->get('error'),
            ],
        ]);
    }

    private function obtenerMenusDelUsuario(Request $request): array
    {
        if (! $request->user()) {
            return [];
        }

        try {
            $roles = $request->user()->roles->pluck('id');

            return Menu::query()
                ->where('activo', true)
                ->whereHas('roles', fn ($consulta) => $consulta->whereIn('roles.id', $roles))
                ->orderBy('orden')
                ->get()
                ->map(fn (Menu $menu) => [
                    'id' => $menu->id,
                    'nombre' => $menu->nombre,
                    'ruta' => $menu->ruta,
                    'icono' => $menu->icono,
                ])
                ->values()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function obtenerTotalVisitas(): int
    {
        try {
            return (int) VisitaPagina::sum('visitas');
        } catch (\Throwable) {
            return 0;
        }
    }
}
