<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\CursoRequest;
use App\Models\Curso;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CursoController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Cursos',
                'descripcion' => 'Cursos de conduccion con precio, duracion y tipo de licencia.',
                'ruta' => 'cursos',
                'campos' => [
                    ['nombre' => 'nombre', 'etiqueta' => 'Nombre', 'tipo' => 'text'],
                    ['nombre' => 'descripcion', 'etiqueta' => 'Descripcion', 'tipo' => 'textarea'],
                    ['nombre' => 'precio', 'etiqueta' => 'Precio', 'tipo' => 'number'],
                    ['nombre' => 'duracion_horas', 'etiqueta' => 'Duracion en horas', 'tipo' => 'number'],
                    ['nombre' => 'tipo_licencia', 'etiqueta' => 'Tipo de licencia', 'tipo' => 'text'],
                    ['nombre' => 'tema_visual', 'etiqueta' => 'Tema visual', 'tipo' => 'select', 'opciones' => $this->opcionesTema()],
                    ['nombre' => 'estado', 'etiqueta' => 'Estado', 'tipo' => 'select', 'opciones' => $this->opcionesEstado()],
                    ['nombre' => 'materias', 'etiqueta' => 'Materias', 'tipo' => 'multiselect', 'opciones' => $this->opcionesMaterias()],
                ],
                'columnas' => ['nombre' => 'Curso', 'precio' => 'Precio', 'tipo_licencia' => 'Licencia', 'estado' => 'Estado'],
            ],
            'registros' => Curso::query()
                ->with('materias')
                ->latest()
                ->paginate(10)
                ->through(fn (Curso $curso) => [
                    'id' => $curso->id,
                    'nombre' => $curso->nombre,
                    'descripcion' => $curso->descripcion,
                    'precio' => $curso->precio,
                    'duracion_horas' => $curso->duracion_horas,
                    'tipo_licencia' => $curso->tipo_licencia,
                    'tema_visual' => $curso->tema_visual,
                    'estado' => $curso->estado,
                    'materias' => $curso->materias->pluck('id')->values(),
                    'materiasTexto' => $curso->materias->pluck('nombre')->join(', '),
                ]),
        ]);
    }

    public function store(CursoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $materias = $datos['materias'] ?? [];
        unset($datos['materias']);

        $curso = Curso::create($datos);
        $curso->materias()->sync($materias);

        return back()->with('exito', 'Curso registrado correctamente.');
    }

    public function update(CursoRequest $request, Curso $curso): RedirectResponse
    {
        $datos = $request->validated();
        $materias = $datos['materias'] ?? [];
        unset($datos['materias']);

        $curso->update($datos);
        $curso->materias()->sync($materias);

        return back()->with('exito', 'Curso actualizado correctamente.');
    }

    public function destroy(Curso $curso): RedirectResponse
    {
        $curso->delete();

        return back()->with('exito', 'Curso eliminado correctamente.');
    }

    private function opcionesMaterias(): array
    {
        return Materia::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn (Materia $materia) => ['valor' => $materia->id, 'etiqueta' => $materia->nombre])
            ->all();
    }

    private function opcionesTema(): array
    {
        return [
            ['valor' => 'ninos', 'etiqueta' => 'Tema Ninos'],
            ['valor' => 'jovenes', 'etiqueta' => 'Tema Jovenes'],
            ['valor' => 'adultos', 'etiqueta' => 'Tema Adultos'],
        ];
    }

    private function opcionesEstado(): array
    {
        return [
            ['valor' => 'disponible', 'etiqueta' => 'Disponible'],
            ['valor' => 'activo', 'etiqueta' => 'Activo'],
            ['valor' => 'finalizado', 'etiqueta' => 'Finalizado'],
            ['valor' => 'cancelado', 'etiqueta' => 'Cancelado'],
        ];
    }
}
