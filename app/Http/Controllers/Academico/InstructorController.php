<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstructorRequest;
use App\Models\Instructor;
use App\Models\Rol;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InstructorController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Instructores',
                'descripcion' => 'Instructores disponibles para ofertas academicas.',
                'ruta' => 'instructores',
                'campos' => [
                    ['nombre' => 'nombre', 'etiqueta' => 'Nombre', 'tipo' => 'text'],
                    ['nombre' => 'apellido', 'etiqueta' => 'Apellido', 'tipo' => 'text'],
                    ['nombre' => 'cedula', 'etiqueta' => 'Cedula', 'tipo' => 'text'],
                    ['nombre' => 'telefono', 'etiqueta' => 'Telefono', 'tipo' => 'text'],
                    ['nombre' => 'especialidad', 'etiqueta' => 'Especialidad', 'tipo' => 'text'],
                    ['nombre' => 'estado', 'etiqueta' => 'Estado', 'tipo' => 'select', 'opciones' => $this->opcionesEstado()],
                ],
                'columnas' => ['nombreCompleto' => 'Instructor', 'cedula' => 'Cedula', 'usuarioTexto' => 'Usuario', 'especialidad' => 'Especialidad', 'estado' => 'Estado'],
            ],
            'registros' => Instructor::query()
                ->with('usuario')
                ->latest()
                ->paginate(10)
                ->through(fn (Instructor $instructor) => [
                    'id' => $instructor->id,
                    'user_id' => $instructor->user_id,
                    'nombre' => $instructor->nombre,
                    'apellido' => $instructor->apellido,
                    'nombreCompleto' => $instructor->nombreCompleto(),
                    'cedula' => $instructor->cedula,
                    'usuarioTexto' => $instructor->usuario?->email ?? 'Sin usuario vinculado',
                    'telefono' => $instructor->telefono,
                    'especialidad' => $instructor->especialidad,
                    'estado' => $instructor->estado,
                ]),
        ]);
    }

    public function store(InstructorRequest $request): RedirectResponse
    {
        $datos = $this->prepararDatosInstructor($request->validated());
        $instructor = Instructor::create($datos);
        $this->asegurarRolInstructor($instructor);

        return back()->with('exito', 'Instructor registrado correctamente.');
    }

    public function update(InstructorRequest $request, Instructor $instructor): RedirectResponse
    {
        $datos = $this->prepararDatosInstructor($request->validated());
        $instructor->update($datos);
        $this->asegurarRolInstructor($instructor->refresh());

        return back()->with('exito', 'Instructor actualizado correctamente.');
    }

    public function destroy(Instructor $instructor): RedirectResponse
    {
        $instructor->delete();

        return back()->with('exito', 'Instructor eliminado correctamente.');
    }

    private function opcionesEstado(): array
    {
        return [
            ['valor' => 'activo', 'etiqueta' => 'Activo'],
            ['valor' => 'inactivo', 'etiqueta' => 'Inactivo'],
        ];
    }

    private function prepararDatosInstructor(array $datos): array
    {
        if (! $this->usuarioEsAdministrador()) {
            unset($datos['user_id']);
        }

        return $datos;
    }

    private function usuarioEsAdministrador(): bool
    {
        return request()->user()?->tieneRol('administrador') ?? false;
    }

    private function asegurarRolInstructor(Instructor $instructor): void
    {
        if (! $instructor->user_id) {
            return;
        }

        $rolInstructor = Rol::firstOrCreate(
            ['nombre' => 'instructor'],
            ['descripcion' => 'Registra asistencia y notas de sus cursos.'],
        );

        $instructor->usuario?->roles()->syncWithoutDetaching([$rolInstructor->id]);
    }

}
