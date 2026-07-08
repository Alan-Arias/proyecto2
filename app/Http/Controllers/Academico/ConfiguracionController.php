<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfiguracionRequest;
use App\Models\Configuracion;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ConfiguracionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Configuracion',
                'descripcion' => 'Configuracion basica usada por el sistema academico.',
                'ruta' => 'configuracion',
                'campos' => [
                    ['nombre' => 'clave', 'etiqueta' => 'Clave', 'tipo' => 'text'],
                    ['nombre' => 'valor', 'etiqueta' => 'Valor', 'tipo' => 'textarea'],
                    ['nombre' => 'tipo', 'etiqueta' => 'Tipo', 'tipo' => 'select', 'opciones' => [
                        ['valor' => 'texto', 'etiqueta' => 'Texto'],
                        ['valor' => 'numero', 'etiqueta' => 'Numero'],
                        ['valor' => 'booleano', 'etiqueta' => 'Booleano'],
                    ]],
                    ['nombre' => 'descripcion', 'etiqueta' => 'Descripcion', 'tipo' => 'text'],
                ],
                'columnas' => ['clave' => 'Clave', 'valor' => 'Valor', 'tipo' => 'Tipo', 'descripcion' => 'Descripcion'],
            ],
            'registros' => Configuracion::query()->latest()->paginate(10),
        ]);
    }

    public function store(ConfiguracionRequest $request): RedirectResponse
    {
        Configuracion::create($request->validated());

        return back()->with('exito', 'Configuracion registrada correctamente.');
    }

    public function update(ConfiguracionRequest $request, Configuracion $configuracion): RedirectResponse
    {
        $configuracion->update($request->validated());

        return back()->with('exito', 'Configuracion actualizada correctamente.');
    }

    public function destroy(Configuracion $configuracion): RedirectResponse
    {
        $configuracion->delete();

        return back()->with('exito', 'Configuracion eliminada correctamente.');
    }
}
