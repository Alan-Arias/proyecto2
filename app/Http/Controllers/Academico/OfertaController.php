<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfertaRequest;
use App\Models\Curso;
use App\Models\Instructor;
use App\Models\Oferta;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OfertaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Ofertas',
                'descripcion' => 'Ofertas academicas por gestion, instructor y cupos.',
                'ruta' => 'ofertas',
                'campos' => [
                    ['nombre' => 'codigo', 'etiqueta' => 'Codigo', 'tipo' => 'text'],
                    ['nombre' => 'course_id', 'etiqueta' => 'Curso', 'tipo' => 'select', 'opciones' => $this->opcionesCursos()],
                    ['nombre' => 'instructor_id', 'etiqueta' => 'Instructor', 'tipo' => 'select', 'opciones' => $this->opcionesInstructores()],
                    ['nombre' => 'fecha_inicio', 'etiqueta' => 'Fecha inicio', 'tipo' => 'date'],
                    ['nombre' => 'fecha_fin', 'etiqueta' => 'Fecha final', 'tipo' => 'date'],
                    ['nombre' => 'cupos', 'etiqueta' => 'Cupos', 'tipo' => 'number'],
                    ['nombre' => 'estado', 'etiqueta' => 'Estado', 'tipo' => 'select', 'opciones' => $this->opcionesEstado()],
                ],
                'columnas' => ['codigo' => 'Oferta', 'cursoTexto' => 'Curso', 'instructorTexto' => 'Instructor', 'cupos' => 'Cupos', 'estado' => 'Estado'],
            ],
            'registros' => Oferta::query()
                ->with(['curso', 'instructor'])
                ->latest()
                ->paginate(10)
                ->through(fn (Oferta $oferta) => [
                    'id' => $oferta->id,
                    'codigo' => $oferta->codigo,
                    'course_id' => $oferta->course_id,
                    'instructor_id' => $oferta->instructor_id,
                    'cursoTexto' => $oferta->curso?->nombre,
                    'instructorTexto' => $oferta->instructor?->nombreCompleto(),
                    'fecha_inicio' => optional($oferta->fecha_inicio)->toDateString(),
                    'fecha_fin' => optional($oferta->fecha_fin)->toDateString(),
                    'cupos' => $oferta->cupos,
                    'cuposDisponibles' => $oferta->cuposDisponibles(),
                    'estado' => $oferta->estado,
                ]),
        ]);
    }

    public function store(OfertaRequest $request): RedirectResponse
    {
        Oferta::create($request->validated());

        return back()->with('exito', 'Oferta registrada correctamente.');
    }

    public function update(OfertaRequest $request, Oferta $oferta): RedirectResponse
    {
        $oferta->update($request->validated());

        return back()->with('exito', 'Oferta actualizada correctamente.');
    }

    public function destroy(Oferta $oferta): RedirectResponse
    {
        $oferta->delete();

        return back()->with('exito', 'Oferta eliminada correctamente.');
    }

    private function opcionesCursos(): array
    {
        return Curso::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn (Curso $curso) => ['valor' => $curso->id, 'etiqueta' => $curso->nombre])
            ->all();
    }

    private function opcionesInstructores(): array
    {
        return Instructor::query()
            ->orderBy('nombre')
            ->get()
            ->map(fn (Instructor $instructor) => ['valor' => $instructor->id, 'etiqueta' => $instructor->nombreCompleto()])
            ->all();
    }

    private function opcionesEstado(): array
    {
        return [
            ['valor' => 'activo', 'etiqueta' => 'Activo'],
            ['valor' => 'finalizado', 'etiqueta' => 'Finalizado'],
            ['valor' => 'cancelado', 'etiqueta' => 'Cancelado'],
        ];
    }
}
