<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\MateriaRequest;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MateriaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Materias',
                'descripcion' => 'Materias teoricas y practicas asociadas a cursos.',
                'ruta' => 'materias',
                'campos' => [
                    ['nombre' => 'nombre', 'etiqueta' => 'Nombre', 'tipo' => 'text'],
                    ['nombre' => 'descripcion', 'etiqueta' => 'Descripcion', 'tipo' => 'textarea'],
                    ['nombre' => 'horas', 'etiqueta' => 'Horas', 'tipo' => 'number'],
                ],
                'columnas' => ['nombre' => 'Materia', 'descripcion' => 'Descripcion', 'horas' => 'Horas'],
            ],
            'registros' => Materia::query()->latest()->paginate(10),
        ]);
    }

    public function store(MateriaRequest $request): RedirectResponse
    {
        Materia::create($request->validated());

        return back()->with('exito', 'Materia registrada correctamente.');
    }

    public function update(MateriaRequest $request, Materia $materia): RedirectResponse
    {
        $materia->update($request->validated());

        return back()->with('exito', 'Materia actualizada correctamente.');
    }

    public function destroy(Materia $materia): RedirectResponse
    {
        $materia->delete();

        return back()->with('exito', 'Materia eliminada correctamente.');
    }
}
