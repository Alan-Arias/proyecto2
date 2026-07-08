<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\AsistenciaRequest;
use App\Models\Asistencia;
use App\Models\Inscripcion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AsistenciaController extends Controller
{
    public function index(): Response
    {
        $consulta = Asistencia::query()
            ->with(['inscripcion.estudiante', 'inscripcion.oferta.curso']);
        $this->aplicarFiltroInstructorAsistencia($consulta);

        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Asistencia',
                'descripcion' => 'Registro simple de asistencia por inscripcion.',
                'ruta' => 'asistencia',
                'campos' => [
                    ['nombre' => 'enrollment_id', 'etiqueta' => 'Inscripcion', 'tipo' => 'select', 'opciones' => $this->opcionesInscripciones()],
                    ['nombre' => 'fecha', 'etiqueta' => 'Fecha', 'tipo' => 'date'],
                    ['nombre' => 'estado', 'etiqueta' => 'Estado', 'tipo' => 'select', 'opciones' => [
                        ['valor' => 'presente', 'etiqueta' => 'Presente'],
                        ['valor' => 'ausente', 'etiqueta' => 'Ausente'],
                        ['valor' => 'licencia', 'etiqueta' => 'Licencia'],
                    ]],
                    ['nombre' => 'observacion', 'etiqueta' => 'Observacion', 'tipo' => 'text'],
                ],
                'columnas' => ['estudianteTexto' => 'Estudiante', 'cursoTexto' => 'Curso', 'fecha' => 'Fecha', 'estado' => 'Estado'],
            ],
            'registros' => $consulta
                ->latest()
                ->paginate(10)
                ->through(fn (Asistencia $asistencia) => [
                    'id' => $asistencia->id,
                    'enrollment_id' => $asistencia->enrollment_id,
                    'fecha' => optional($asistencia->fecha)->toDateString(),
                    'estado' => $asistencia->estado,
                    'observacion' => $asistencia->observacion,
                    'estudianteTexto' => $asistencia->inscripcion?->estudiante?->nombreCompleto(),
                    'cursoTexto' => $asistencia->inscripcion?->oferta?->curso?->nombre,
                ]),
        ]);
    }

    public function store(AsistenciaRequest $request): RedirectResponse
    {
        $this->verificarAccesoInscripcion((int) $request->input('enrollment_id'));

        Asistencia::updateOrCreate(
            [
                'enrollment_id' => $request->input('enrollment_id'),
                'fecha' => $request->input('fecha'),
            ],
            $request->validated()
        );

        return back()->with('exito', 'Asistencia registrada correctamente.');
    }

    public function update(AsistenciaRequest $request, Asistencia $asistencia): RedirectResponse
    {
        $this->verificarAccesoAsistencia($asistencia);
        $this->verificarAccesoInscripcion((int) $request->input('enrollment_id'));

        $asistencia->update($request->validated());

        return back()->with('exito', 'Asistencia actualizada correctamente.');
    }

    public function destroy(Asistencia $asistencia): RedirectResponse
    {
        $this->verificarAccesoAsistencia($asistencia);

        $asistencia->delete();

        return back()->with('exito', 'Asistencia eliminada correctamente.');
    }

    private function opcionesInscripciones(): array
    {
        $consulta = Inscripcion::query()
            ->with(['estudiante', 'oferta.curso'])
            ->latest();
        $this->aplicarFiltroInstructorInscripcion($consulta);

        return $consulta
            ->get()
            ->map(fn (Inscripcion $inscripcion) => [
                'valor' => $inscripcion->id,
                'etiqueta' => $inscripcion->estudiante?->nombreCompleto().' - '.$inscripcion->oferta?->curso?->nombre,
            ])
            ->all();
    }

    private function aplicarFiltroInstructorAsistencia(Builder $consulta): void
    {
        $instructorId = $this->instructorIdParaFiltro();

        if ($instructorId === null) {
            return;
        }

        if ($instructorId === 0) {
            $consulta->whereKey(0);

            return;
        }

        $consulta->whereHas('inscripcion.oferta', fn (Builder $consultaOferta) => $consultaOferta->where('instructor_id', $instructorId));
    }

    private function aplicarFiltroInstructorInscripcion(Builder $consulta): void
    {
        $instructorId = $this->instructorIdParaFiltro();

        if ($instructorId === null) {
            return;
        }

        if ($instructorId === 0) {
            $consulta->whereKey(0);

            return;
        }

        $consulta->whereHas('oferta', fn (Builder $consultaOferta) => $consultaOferta->where('instructor_id', $instructorId));
    }

    private function verificarAccesoInscripcion(int $inscripcionId): void
    {
        $instructorId = $this->instructorIdParaFiltro();

        if ($instructorId === null) {
            return;
        }

        $permitida = $instructorId !== 0
            && Inscripcion::query()
                ->whereKey($inscripcionId)
                ->whereHas('oferta', fn (Builder $consultaOferta) => $consultaOferta->where('instructor_id', $instructorId))
                ->exists();

        if (! $permitida) {
            abort(403, 'El instructor solo puede gestionar asistencia de sus cursos asignados.');
        }
    }

    private function verificarAccesoAsistencia(Asistencia $asistencia): void
    {
        $instructorId = $this->instructorIdParaFiltro();

        if ($instructorId === null) {
            return;
        }

        $permitida = $instructorId !== 0
            && Asistencia::query()
                ->whereKey($asistencia->id)
                ->whereHas('inscripcion.oferta', fn (Builder $consultaOferta) => $consultaOferta->where('instructor_id', $instructorId))
                ->exists();

        if (! $permitida) {
            abort(403, 'El instructor solo puede gestionar asistencia de sus cursos asignados.');
        }
    }

    private function instructorIdParaFiltro(): ?int
    {
        $usuario = request()->user();

        if (! $usuario?->tieneRol('instructor') || $usuario->tieneRol('administrador')) {
            return null;
        }

        return $usuario->instructor?->id ?? 0;
    }
}
