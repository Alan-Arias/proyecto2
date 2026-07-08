<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolRequest;
use App\Models\Rol;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RolController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Roles',
                'descripcion' => 'Roles de negocio y rol tecnico del sistema.',
                'ruta' => 'roles',
                'campos' => [
                    ['nombre' => 'nombre', 'etiqueta' => 'Nombre', 'tipo' => 'text'],
                    ['nombre' => 'descripcion', 'etiqueta' => 'Descripcion', 'tipo' => 'textarea'],
                ],
                'columnas' => ['nombre' => 'Nombre', 'descripcion' => 'Descripcion'],
            ],
            'registros' => Rol::query()->latest()->paginate(10),
        ]);
    }

    public function store(RolRequest $request): RedirectResponse
    {
        Rol::create($request->validated());

        return back()->with('exito', 'Rol registrado correctamente.');
    }

    public function update(RolRequest $request, Rol $role): RedirectResponse
    {
        $role->update($request->validated());

        return back()->with('exito', 'Rol actualizado correctamente.');
    }

    public function destroy(Rol $role): RedirectResponse
    {
        $role->delete();

        return back()->with('exito', 'Rol eliminado correctamente.');
    }
}
