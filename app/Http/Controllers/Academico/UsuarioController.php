<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsuarioRequest;
use App\Models\Estudiante;
use App\Models\Instructor;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UsuarioController extends Controller
{
    public function index(): Response
    {
        $registros = User::query()
            ->with(['roles', 'instructor', 'estudiante'])
            ->latest()
            ->paginate(10)
            ->through(fn (User $usuario) => $this->formatearUsuario($usuario));

        if ((int) request('page', 1) === 1) {
            $registros->setCollection(
                $registros->getCollection()->concat($this->personasPendientesComoUsuarios())
            );
        }

        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Usuarios',
                'descripcion' => 'Gestion de usuarios, roles y estado de acceso.',
                'ruta' => 'usuarios',
                'campos' => [
                    ['nombre' => 'name', 'etiqueta' => 'Nombre', 'tipo' => 'text'],
                    ['nombre' => 'email', 'etiqueta' => 'Correo electronico', 'tipo' => 'email'],
                    ['nombre' => 'telefono', 'etiqueta' => 'Telefono', 'tipo' => 'text'],
                    ['nombre' => 'password', 'etiqueta' => 'Contrasena', 'tipo' => 'password'],
                    ['nombre' => 'activo', 'etiqueta' => 'Activo', 'tipo' => 'checkbox'],
                    ['nombre' => 'roles', 'etiqueta' => 'Roles', 'tipo' => 'multiselect', 'opciones' => $this->opcionesRoles()],
                    ['nombre' => 'persona_tipo', 'etiqueta' => 'Tipo de persona', 'tipo' => 'hidden'],
                    ['nombre' => 'persona_id', 'etiqueta' => 'Persona', 'tipo' => 'hidden'],
                ],
                'columnas' => ['name' => 'Nombre', 'email' => 'Correo', 'rolesTexto' => 'Roles', 'personaTexto' => 'Persona vinculada', 'estadoTexto' => 'Estado'],
            ],
            'registros' => $registros,
        ]);
    }

    public function store(UsuarioRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $roles = $datos['roles'];
        $personaTipo = $datos['persona_tipo'] ?? null;
        $personaId = $datos['persona_id'] ?? null;
        unset($datos['roles'], $datos['persona_tipo'], $datos['persona_id']);

        $datos['activo'] = $request->boolean('activo', true);
        $datos['password'] = Hash::make($datos['password']);

        $roles = $this->agregarRolSegunPersona($roles, $personaTipo, $personaId);
        $usuario = User::create($datos);
        $usuario->roles()->sync($roles);
        $this->vincularPersona($usuario, $personaTipo, $personaId);

        return back()->with('exito', 'Usuario registrado correctamente.');
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        $datos = $request->validated();
        $roles = $datos['roles'];
        $personaTipo = $datos['persona_tipo'] ?? null;
        $personaId = $datos['persona_id'] ?? null;
        unset($datos['roles'], $datos['persona_tipo'], $datos['persona_id']);

        $datos['activo'] = $request->boolean('activo');

        if (! empty($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        } else {
            unset($datos['password']);
        }

        $usuario->update($datos);
        $roles = $this->agregarRolSegunPersona($roles, $personaTipo, $personaId);
        $usuario->roles()->sync($roles);
        $this->vincularPersona($usuario, $personaTipo, $personaId);

        return back()->with('exito', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        $usuario->delete();

        return back()->with('exito', 'Usuario eliminado correctamente.');
    }

    private function opcionesRoles(): array
    {
        return Rol::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn (Rol $rol) => ['valor' => $rol->id, 'etiqueta' => ucfirst($rol->nombre)])
            ->all();
    }

    private function formatearUsuario(User $usuario): array
    {
        $persona = $this->obtenerPersonaDelUsuario($usuario);

        return [
            'id' => $usuario->id,
            'name' => $usuario->name,
            'email' => $usuario->email,
            'telefono' => $usuario->telefono,
            'password' => '',
            'activo' => $usuario->activo,
            'estadoTexto' => $usuario->activo ? 'Activo' : 'Inactivo',
            'roles' => $usuario->roles->pluck('id')->values(),
            'rolesTexto' => $usuario->roles->pluck('nombre')->join(', '),
            'persona_tipo' => $persona['tipo'],
            'persona_id' => $persona['id'],
            'personaTexto' => $persona['texto'],
        ];
    }

    private function personasPendientesComoUsuarios()
    {
        $rolInstructor = Rol::firstOrCreate(
            ['nombre' => 'instructor'],
            ['descripcion' => 'Registra asistencia y notas de sus cursos.'],
        );
        $rolEstudiante = Rol::firstOrCreate(
            ['nombre' => 'estudiante'],
            ['descripcion' => 'Consulta sus cursos, notas, pagos y certificados.'],
        );

        $instructores = Instructor::query()
            ->whereNull('user_id')
            ->orderBy('nombre')
            ->get()
            ->map(fn (Instructor $instructor) => [
                'id' => 'instructor-pendiente-'.$instructor->id,
                'name' => $instructor->nombreCompleto(),
                'email' => 'Pendiente de correo',
                'telefono' => $instructor->telefono,
                'password' => '',
                'activo' => true,
                'estadoTexto' => 'Pendiente de usuario',
                'roles' => [$rolInstructor->id],
                'rolesTexto' => 'instructor',
                'persona_tipo' => 'instructor',
                'persona_id' => $instructor->id,
                'personaTexto' => 'Instructor: '.$instructor->nombreCompleto().' (sin usuario)',
                'crearUsuarioDesdePersona' => true,
                'ocultarEliminar' => true,
            ]);

        $estudiantes = Estudiante::query()
            ->whereNull('user_id')
            ->orderBy('nombre')
            ->get()
            ->map(fn (Estudiante $estudiante) => [
                'id' => 'estudiante-pendiente-'.$estudiante->id,
                'name' => $estudiante->nombreCompleto(),
                'email' => 'Pendiente de correo',
                'telefono' => $estudiante->telefono,
                'password' => '',
                'activo' => true,
                'estadoTexto' => 'Pendiente de usuario',
                'roles' => [$rolEstudiante->id],
                'rolesTexto' => 'estudiante',
                'persona_tipo' => 'estudiante',
                'persona_id' => $estudiante->id,
                'personaTexto' => 'Estudiante: '.$estudiante->nombreCompleto().' (sin usuario)',
                'crearUsuarioDesdePersona' => true,
                'ocultarEliminar' => true,
            ]);

        return $instructores->concat($estudiantes)->values();
    }

    private function agregarRolSegunPersona(array $roles, ?string $personaTipo, ?int $personaId): array
    {
        if (! $personaTipo || ! $personaId) {
            return $roles;
        }

        $rol = Rol::firstOrCreate(
            ['nombre' => $personaTipo],
            ['descripcion' => $personaTipo === 'instructor'
                ? 'Registra asistencia y notas de sus cursos.'
                : 'Consulta sus cursos, notas, pagos y certificados.'],
        );

        return collect($roles)
            ->push($rol->id)
            ->unique()
            ->values()
            ->all();
    }

    private function vincularPersona(User $usuario, ?string $personaTipo, ?int $personaId): void
    {
        if (! $personaTipo || ! $personaId) {
            return;
        }

        if ($personaTipo === 'instructor') {
            Instructor::where('user_id', $usuario->id)
                ->where('id', '!=', $personaId)
                ->update(['user_id' => null]);
            Instructor::whereKey($personaId)->update(['user_id' => $usuario->id]);
            return;
        }

        Estudiante::where('user_id', $usuario->id)
            ->where('id', '!=', $personaId)
            ->update(['user_id' => null]);
        Estudiante::whereKey($personaId)->update(['user_id' => $usuario->id]);
    }

    private function obtenerPersonaDelUsuario(User $usuario): array
    {
        if ($usuario->instructor) {
            return [
                'tipo' => 'instructor',
                'id' => $usuario->instructor->id,
                'texto' => 'Instructor: '.$usuario->instructor->nombreCompleto(),
            ];
        }

        if ($usuario->estudiante) {
            return [
                'tipo' => 'estudiante',
                'id' => $usuario->estudiante->id,
                'texto' => 'Estudiante: '.$usuario->estudiante->nombreCompleto(),
            ];
        }

        return [
            'tipo' => '',
            'id' => '',
            'texto' => 'No vinculado',
        ];
    }
}
