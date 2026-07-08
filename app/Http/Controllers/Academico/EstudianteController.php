<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\EstudianteRequest;
use App\Models\Estudiante;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class EstudianteController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Estudiantes',
                'descripcion' => 'Registro de estudiantes de la autoescuela.',
                'ruta' => 'estudiantes',
                'campos' => [
                    ['nombre' => 'nombre', 'etiqueta' => 'Nombre', 'tipo' => 'text'],
                    ['nombre' => 'apellido', 'etiqueta' => 'Apellido', 'tipo' => 'text'],
                    ['nombre' => 'cedula', 'etiqueta' => 'Cedula', 'tipo' => 'text'],
                    ['nombre' => 'email', 'etiqueta' => 'Correo electronico', 'tipo' => 'email'],
                    ['nombre' => 'password', 'etiqueta' => 'Contrasena inicial (opcional)', 'tipo' => 'password'],
                    ['nombre' => 'telefono', 'etiqueta' => 'Telefono', 'tipo' => 'text'],
                    ['nombre' => 'direccion', 'etiqueta' => 'Direccion', 'tipo' => 'text'],
                    ['nombre' => 'fecha_nacimiento', 'etiqueta' => 'Fecha de nacimiento', 'tipo' => 'date'],
                    ['nombre' => 'estado', 'etiqueta' => 'Estado', 'tipo' => 'select', 'opciones' => $this->opcionesEstado()],
                ],
                'columnas' => ['nombreCompleto' => 'Estudiante', 'email' => 'Correo', 'cedula' => 'Cedula', 'telefono' => 'Telefono', 'estado' => 'Estado'],
            ],
            'registros' => Estudiante::query()
                ->with('usuario')
                ->latest()
                ->paginate(10)
                ->through(fn (Estudiante $estudiante) => [
                    'id' => $estudiante->id,
                    'nombre' => $estudiante->nombre,
                    'apellido' => $estudiante->apellido,
                    'nombreCompleto' => $estudiante->nombreCompleto(),
                    'cedula' => $estudiante->cedula,
                    'email' => $estudiante->usuario?->email,
                    'password' => '',
                    'telefono' => $estudiante->telefono,
                    'direccion' => $estudiante->direccion,
                    'fecha_nacimiento' => optional($estudiante->fecha_nacimiento)->toDateString(),
                    'estado' => $estudiante->estado,
                ]),
        ]);
    }

    public function store(EstudianteRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $usuario = $this->crearOActualizarUsuarioEstudiante($datos);

        Estudiante::create($this->datosEstudiante($datos) + [
            'user_id' => $usuario->id,
        ]);

        return back()->with('exito', 'Estudiante registrado correctamente. Puede ingresar con su correo y la contrasena inicial indicada.');
    }

    public function update(EstudianteRequest $request, Estudiante $estudiante): RedirectResponse
    {
        $datos = $request->validated();
        $usuario = $this->crearOActualizarUsuarioEstudiante($datos, $estudiante->usuario);

        $estudiante->update($this->datosEstudiante($datos) + [
            'user_id' => $usuario->id,
        ]);

        return back()->with('exito', 'Estudiante actualizado correctamente.');
    }

    public function destroy(Estudiante $estudiante): RedirectResponse
    {
        $estudiante->delete();

        return back()->with('exito', 'Estudiante eliminado correctamente.');
    }

    private function opcionesEstado(): array
    {
        return [
            ['valor' => 'activo', 'etiqueta' => 'Activo'],
            ['valor' => 'inactivo', 'etiqueta' => 'Inactivo'],
        ];
    }

    private function crearOActualizarUsuarioEstudiante(array $datos, ?User $usuario = null): User
    {
        $rolEstudiante = Rol::firstOrCreate(
            ['nombre' => 'estudiante'],
            ['descripcion' => 'Consulta sus cursos, notas, pagos y certificados.'],
        );

        $datosUsuario = [
            'name' => trim($datos['nombre'].' '.$datos['apellido']),
            'email' => $datos['email'],
            'telefono' => $datos['telefono'] ?? null,
            'activo' => ($datos['estado'] ?? 'activo') === 'activo',
        ];

        if (! empty($datos['password'])) {
            $datosUsuario['password'] = Hash::make($datos['password']);
        } elseif (! $usuario) {
            $datosUsuario['password'] = Hash::make($datos['cedula']);
        }

        if ($usuario) {
            $usuario->update($datosUsuario);
        } else {
            $usuario = User::create($datosUsuario);
        }

        $usuario->roles()->syncWithoutDetaching([$rolEstudiante->id]);

        return $usuario;
    }

    private function datosEstudiante(array $datos): array
    {
        return collect($datos)
            ->only(['nombre', 'apellido', 'cedula', 'telefono', 'direccion', 'fecha_nacimiento', 'estado'])
            ->all();
    }
}
