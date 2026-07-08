<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalificacionRequest;
use App\Models\Calificacion;
use App\Models\Inscripcion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CalificacionController extends Controller
{
    public function index(): Response
    {
        $soloLectura = $this->usuarioEsEstudiante();
        $consulta = Calificacion::query()
            ->with(['inscripcion.estudiante', 'inscripcion.oferta.curso']);
        $this->aplicarFiltroEstudiante($consulta);
        $this->aplicarFiltroInstructorCalificacion($consulta);

        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Calificaciones',
                'descripcion' => 'Registro de notas. La nota minima de aprobacion es 51.',
                'ruta' => 'calificaciones',
                'soloLectura' => $soloLectura,
                'campos' => [
                    ['nombre' => 'enrollment_id', 'etiqueta' => 'Inscripcion', 'tipo' => 'select', 'opciones' => $this->opcionesInscripciones()],
                    ['nombre' => 'nota', 'etiqueta' => 'Nota', 'tipo' => 'number'],
                    ['nombre' => 'observacion', 'etiqueta' => 'Observacion', 'tipo' => 'text'],
                ],
                'columnas' => ['estudianteTexto' => 'Estudiante', 'cursoTexto' => 'Curso', 'nota' => 'Nota', 'estado' => 'Estado'],
            ],
            'registros' => $consulta
                ->latest()
                ->paginate(10)
                ->through(fn (Calificacion $calificacion) => [
                    'id' => $calificacion->id,
                    'enrollment_id' => $calificacion->enrollment_id,
                    'nota' => $calificacion->nota,
                    'estado' => $calificacion->estado,
                    'observacion' => $calificacion->observacion,
                    'estudianteTexto' => $calificacion->inscripcion?->estudiante?->nombreCompleto(),
                    'cursoTexto' => $calificacion->inscripcion?->oferta?->curso?->nombre,
                ]),
        ]);
    }

    public function store(CalificacionRequest $request): RedirectResponse
    {
        $this->bloquearGestionEstudiante();
        $this->verificarAccesoInscripcion((int) $request->input('enrollment_id'));

        $datos = $request->validated();
        $datos['estado'] = (float) $datos['nota'] >= 51 ? 'aprobado' : 'reprobado';

        $calificacion = Calificacion::updateOrCreate(
            ['enrollment_id' => $datos['enrollment_id']],
            $datos
        );

        $calificacion->inscripcion?->update([
            'estado_academico' => $datos['estado'],
        ]);

        return back()->with('exito', 'Calificacion registrada correctamente.');
    }

    public function update(CalificacionRequest $request, Calificacion $calificacion): RedirectResponse
    {
        $this->bloquearGestionEstudiante();
        $this->verificarAccesoCalificacion($calificacion);
        $this->verificarAccesoInscripcion((int) $request->input('enrollment_id'));

        $datos = $request->validated();
        $datos['estado'] = (float) $datos['nota'] >= 51 ? 'aprobado' : 'reprobado';

        $calificacion->update($datos);
        $calificacion->inscripcion?->update([
            'estado_academico' => $datos['estado'],
        ]);

        return back()->with('exito', 'Calificacion actualizada correctamente.');
    }

    public function destroy(Calificacion $calificacion): RedirectResponse
    {
        $this->bloquearGestionEstudiante();
        $this->verificarAccesoCalificacion($calificacion);

        $calificacion->delete();

        return back()->with('exito', 'Calificacion eliminada correctamente.');
    }

    private function opcionesInscripciones(): array
    {
        $consulta = Inscripcion::query()
            ->with(['estudiante', 'oferta.curso'])
            ->latest();
        $this->aplicarFiltroEstudianteInscripcion($consulta);
        $this->aplicarFiltroInstructorInscripcion($consulta);

        return $consulta
            ->get()
            ->map(fn (Inscripcion $inscripcion) => [
                'valor' => $inscripcion->id,
                'etiqueta' => $inscripcion->estudiante?->nombreCompleto().' - '.$inscripcion->oferta?->curso?->nombre,
            ])
            ->all();
    }

    private function usuarioEsEstudiante(): bool
    {
        return request()->user()?->tieneRol('estudiante') ?? false;
    }

    private function aplicarFiltroEstudiante(Builder $consulta): void
    {
        if ($this->usuarioEsEstudiante()) {
            $consulta->whereHas('inscripcion.estudiante', fn ($consultaEstudiante) => $consultaEstudiante->where('user_id', request()->user()->id));
        }
    }

    private function aplicarFiltroEstudianteInscripcion(Builder $consulta): void
    {
        if ($this->usuarioEsEstudiante()) {
            $consulta->whereHas('estudiante', fn ($consultaEstudiante) => $consultaEstudiante->where('user_id', request()->user()->id));
        }
    }

    private function aplicarFiltroInstructorCalificacion(Builder $consulta): void
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
            abort(403, 'El instructor solo puede gestionar calificaciones de sus cursos asignados.');
        }
    }

    private function verificarAccesoCalificacion(Calificacion $calificacion): void
    {
        $instructorId = $this->instructorIdParaFiltro();

        if ($instructorId === null) {
            return;
        }

        $permitida = $instructorId !== 0
            && Calificacion::query()
                ->whereKey($calificacion->id)
                ->whereHas('inscripcion.oferta', fn (Builder $consultaOferta) => $consultaOferta->where('instructor_id', $instructorId))
                ->exists();

        if (! $permitida) {
            abort(403, 'El instructor solo puede gestionar calificaciones de sus cursos asignados.');
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

    private function bloquearGestionEstudiante(): void
    {
        if ($this->usuarioEsEstudiante()) {
            abort(403, 'El estudiante solo puede consultar sus calificaciones.');
        }
    }
}
