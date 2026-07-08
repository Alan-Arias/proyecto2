<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use App\Models\Rol;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Menu dinamico',
                'descripcion' => 'Opciones del menu asociadas a roles del sistema.',
                'ruta' => 'menus',
                'campos' => [
                    ['nombre' => 'nombre', 'etiqueta' => 'Nombre', 'tipo' => 'text'],
                    ['nombre' => 'ruta', 'etiqueta' => 'Nombre de ruta Laravel', 'tipo' => 'text'],
                    ['nombre' => 'icono', 'etiqueta' => 'Icono simple', 'tipo' => 'text'],
                    ['nombre' => 'orden', 'etiqueta' => 'Orden', 'tipo' => 'number'],
                    ['nombre' => 'activo', 'etiqueta' => 'Activo', 'tipo' => 'checkbox'],
                    ['nombre' => 'roles', 'etiqueta' => 'Roles', 'tipo' => 'multiselect', 'opciones' => $this->opcionesRoles()],
                ],
                'columnas' => ['nombre' => 'Nombre', 'ruta' => 'Ruta', 'rolesTexto' => 'Roles', 'estadoTexto' => 'Estado'],
            ],
            'registros' => Menu::query()
                ->with('roles')
                ->orderBy('orden')
                ->paginate(10)
                ->through(fn (Menu $menu) => [
                    'id' => $menu->id,
                    'nombre' => $menu->nombre,
                    'ruta' => $menu->ruta,
                    'icono' => $menu->icono,
                    'orden' => $menu->orden,
                    'activo' => $menu->activo,
                    'estadoTexto' => $menu->activo ? 'Activo' : 'Inactivo',
                    'roles' => $menu->roles->pluck('id')->values(),
                    'rolesTexto' => $menu->roles->pluck('nombre')->join(', '),
                ]),
        ]);
    }

    public function store(MenuRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $roles = $datos['roles'];
        unset($datos['roles']);

        $datos['activo'] = $request->boolean('activo', true);
        $menu = Menu::create($datos);
        $menu->roles()->sync($roles);

        return back()->with('exito', 'Opcion de menu registrada correctamente.');
    }

    public function update(MenuRequest $request, Menu $menu): RedirectResponse
    {
        $datos = $request->validated();
        $roles = $datos['roles'];
        unset($datos['roles']);

        $datos['activo'] = $request->boolean('activo');
        $menu->update($datos);
        $menu->roles()->sync($roles);

        return back()->with('exito', 'Opcion de menu actualizada correctamente.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return back()->with('exito', 'Opcion de menu eliminada correctamente.');
    }

    private function opcionesRoles(): array
    {
        return Rol::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn (Rol $rol) => ['valor' => $rol->id, 'etiqueta' => ucfirst($rol->nombre)])
            ->all();
    }
}
