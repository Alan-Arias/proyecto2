<?php

namespace Database\Seeders;

use App\Models\Asistencia;
use App\Models\Calificacion;
use App\Models\Certificado;
use App\Models\Configuracion;
use App\Models\Curso;
use App\Models\CuotaPago;
use App\Models\Estudiante;
use App\Models\Instructor;
use App\Models\Inscripcion;
use App\Models\Materia;
use App\Models\Menu;
use App\Models\MetodoPago;
use App\Models\Oferta;
use App\Models\Pago;
use App\Models\PlanPago;
use App\Models\Rol;
use App\Models\User;
use App\Models\VisitaPagina;
use App\Services\PagoFacilService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = collect([
            ['nombre' => 'administrador', 'descripcion' => 'Rol tecnico para usuarios y configuracion.'],
            ['nombre' => 'propietario', 'descripcion' => 'Revisa dashboard, ingresos y reportes generales.'],
            ['nombre' => 'secretaria', 'descripcion' => 'Gestiona estudiantes, cursos, inscripciones y pagos.'],
            ['nombre' => 'instructor', 'descripcion' => 'Registra asistencia y notas de sus cursos.'],
            ['nombre' => 'estudiante', 'descripcion' => 'Consulta sus cursos, notas, pagos y certificados.'],
        ])->mapWithKeys(fn (array $rol) => [$rol['nombre'] => Rol::create($rol)]);

        $this->crearUsuariosDePrueba($roles);
        $this->crearMenuDinamico($roles);

        $materias = collect([
            Materia::create(['nombre' => 'Normativa de Transito', 'descripcion' => 'Senales, reglas y responsabilidades viales.', 'horas' => 12]),
            Materia::create(['nombre' => 'Mecanica Basica', 'descripcion' => 'Revision del vehiculo antes de conducir.', 'horas' => 8]),
            Materia::create(['nombre' => 'Practica de Manejo', 'descripcion' => 'Practicas guiadas en circuito y via urbana.', 'horas' => 20]),
        ]);

        $cursoBasico = Curso::create([
            'nombre' => 'Curso de Conduccion Categoria B',
            'descripcion' => 'Curso teorico-practico para nuevos conductores.',
            'precio' => 850,
            'duracion_horas' => 40,
            'tipo_licencia' => 'B',
            'tema_visual' => 'jovenes',
            'estado' => 'disponible',
        ]);
        $cursoBasico->materias()->sync($materias->pluck('id'));

        $cursoAdultos = Curso::create([
            'nombre' => 'Curso de Refuerzo para Adultos',
            'descripcion' => 'Refuerzo practico para personas que desean recuperar confianza.',
            'precio' => 650,
            'duracion_horas' => 28,
            'tipo_licencia' => 'Particular',
            'tema_visual' => 'adultos',
            'estado' => 'activo',
        ]);
        $cursoAdultos->materias()->sync([$materias[0]->id, $materias[2]->id]);

        $usuarioInstructor = User::where('email', 'instructor@autoescuela.test')->first();
        $instructor = Instructor::create([
            'user_id' => $usuarioInstructor?->id,
            'nombre' => 'Carlos',
            'apellido' => 'Mamani',
            'cedula' => '7894561',
            'telefono' => '72100001',
            'especialidad' => 'Practica urbana',
            'estado' => 'activo',
        ]);

        $usuarioEstudiante = User::where('email', 'estudiante@autoescuela.test')->first();
        $estudiante = Estudiante::create([
            'user_id' => $usuarioEstudiante?->id,
            'nombre' => 'Lucia',
            'apellido' => 'Rivera',
            'cedula' => '4561237',
            'telefono' => '72100002',
            'direccion' => 'Barrio Centro, Camiri',
            'fecha_nacimiento' => '2005-04-12',
            'estado' => 'activo',
        ]);

        Estudiante::create([
            'nombre' => 'Marco',
            'apellido' => 'Vargas',
            'cedula' => '9988776',
            'telefono' => '72100003',
            'direccion' => 'Zona Sur, Camiri',
            'fecha_nacimiento' => '1999-09-20',
            'estado' => 'activo',
        ]);

        $oferta = Oferta::create([
            'course_id' => $cursoBasico->id,
            'instructor_id' => $instructor->id,
            'codigo' => '1-2026',
            'fecha_inicio' => Carbon::now()->addDays(5)->toDateString(),
            'fecha_fin' => Carbon::now()->addDays(35)->toDateString(),
            'cupos' => 15,
            'estado' => 'activo',
        ]);

        Oferta::create([
            'course_id' => $cursoAdultos->id,
            'instructor_id' => $instructor->id,
            'codigo' => '2-2026',
            'fecha_inicio' => Carbon::now()->addDays(12)->toDateString(),
            'fecha_fin' => Carbon::now()->addDays(45)->toDateString(),
            'cupos' => 10,
            'estado' => 'activo',
        ]);

        $inscripcion = Inscripcion::create([
            'student_id' => $estudiante->id,
            'offer_id' => $oferta->id,
            'fecha_inscripcion' => Carbon::now()->toDateString(),
            'estado_academico' => 'inscrito',
            'estado_pago' => 'pendiente',
            'precio_total' => $cursoBasico->precio,
            'saldo_pendiente' => $cursoBasico->precio,
        ]);

        Asistencia::create([
            'enrollment_id' => $inscripcion->id,
            'fecha' => Carbon::now()->toDateString(),
            'estado' => 'presente',
            'observacion' => 'Primera clase asistida.',
        ]);

        $calificacion = Calificacion::create([
            'enrollment_id' => $inscripcion->id,
            'nota' => 78,
            'estado' => 'aprobado',
            'observacion' => 'Cumple la nota minima de aprobacion.',
        ]);

        Certificado::create([
            'enrollment_id' => $inscripcion->id,
            'codigo' => 'CERT-'.now()->format('Ymd').'-001',
            'fecha_emision' => Carbon::now()->toDateString(),
            'estado' => $calificacion->definirEstado() === 'aprobado' ? 'emitido' : 'pendiente',
        ]);

        $metodoEfectivo = MetodoPago::create(['nombre' => 'Efectivo', 'descripcion' => 'Pago al contado en oficina.']);
        MetodoPago::create(['nombre' => 'QR', 'descripcion' => 'Pago por QR mediante PagoFacil academico.']);
        MetodoPago::create(['nombre' => 'Cuotas', 'descripcion' => 'Pago dividido en dos o mas cuotas.']);

        Pago::create([
            'enrollment_id' => $inscripcion->id,
            'payment_method_id' => $metodoEfectivo->id,
            'monto' => 300,
            'fecha_pago' => Carbon::now()->toDateString(),
            'referencia' => 'REC-001',
            'comprobante' => 'Comprobante simple 001',
            'estado' => 'registrado',
        ]);
        $inscripcion->actualizarEstadoPago();

        $modoPagoFacil = config('services.pagofacil.modo');
        config(['services.pagofacil.modo' => 'simulado']);
        app(PagoFacilService::class)->generarQrPago($inscripcion->fresh(), 200, 'QR-DEMO-001');
        config(['services.pagofacil.modo' => $modoPagoFacil]);

        $planPago = PlanPago::create([
            'enrollment_id' => $inscripcion->id,
            'monto_total' => $inscripcion->saldo_pendiente,
            'numero_cuotas' => 2,
            'estado' => 'activo',
        ]);

        CuotaPago::create([
            'payment_plan_id' => $planPago->id,
            'numero_cuota' => 1,
            'monto' => 275,
            'fecha_vencimiento' => Carbon::now()->addDays(15)->toDateString(),
            'estado' => 'pendiente',
        ]);

        CuotaPago::create([
            'payment_plan_id' => $planPago->id,
            'numero_cuota' => 2,
            'monto' => 275,
            'fecha_vencimiento' => Carbon::now()->addDays(30)->toDateString(),
            'estado' => 'pendiente',
        ]);

        foreach (['Inicio', 'Cursos', 'Dashboard', 'Pagos', 'Reportes'] as $pagina) {
            VisitaPagina::create(['pagina' => $pagina, 'visitas' => 5]);
        }

        Configuracion::create([
            'clave' => 'nombre_autoescuela',
            'valor' => 'Autoescuela America',
            'tipo' => 'texto',
            'descripcion' => 'Nombre visible del sistema.',
        ]);
    }

    private function crearUsuariosDePrueba($roles): void
    {
        $usuarios = [
            ['name' => 'Administrador Academico', 'email' => 'admin@autoescuela.test', 'rol' => 'administrador'],
            ['name' => 'Propietario America', 'email' => 'propietario@autoescuela.test', 'rol' => 'propietario'],
            ['name' => 'Secretaria America', 'email' => 'secretaria@autoescuela.test', 'rol' => 'secretaria'],
            ['name' => 'Instructor America', 'email' => 'instructor@autoescuela.test', 'rol' => 'instructor'],
            ['name' => 'Estudiante America', 'email' => 'estudiante@autoescuela.test', 'rol' => 'estudiante'],
        ];

        foreach ($usuarios as $datosUsuario) {
            $usuario = User::create([
                'name' => $datosUsuario['name'],
                'email' => $datosUsuario['email'],
                'telefono' => '70000000',
                'password' => Hash::make('password'),
                'activo' => true,
            ]);

            $usuario->roles()->attach($roles[$datosUsuario['rol']]->id);
        }
    }

    private function crearMenuDinamico($roles): void
    {
        $menuPorRol = [
            ['nombre' => 'Dashboard', 'ruta' => 'dashboard', 'icono' => 'panel', 'roles' => ['administrador', 'propietario', 'secretaria', 'instructor', 'estudiante']],
            ['nombre' => 'Usuarios', 'ruta' => 'usuarios.index', 'icono' => 'usuarios', 'roles' => ['administrador']],
            ['nombre' => 'Roles', 'ruta' => 'roles.index', 'icono' => 'roles', 'roles' => ['administrador']],
            ['nombre' => 'Estudiantes', 'ruta' => 'estudiantes.index', 'icono' => 'estudiantes', 'roles' => ['administrador', 'secretaria']],
            ['nombre' => 'Instructores', 'ruta' => 'instructores.index', 'icono' => 'instructores', 'roles' => ['administrador', 'secretaria']],
            ['nombre' => 'Materias', 'ruta' => 'materias.index', 'icono' => 'materias', 'roles' => ['administrador', 'secretaria']],
            ['nombre' => 'Cursos', 'ruta' => 'cursos.index', 'icono' => 'cursos', 'roles' => ['administrador', 'secretaria', 'propietario']],
            ['nombre' => 'Ofertas', 'ruta' => 'ofertas.index', 'icono' => 'ofertas', 'roles' => ['administrador', 'secretaria']],
            ['nombre' => 'Inscripciones', 'ruta' => 'inscripciones.index', 'icono' => 'inscripciones', 'roles' => ['administrador', 'secretaria', 'estudiante']],
            ['nombre' => 'Asistencia', 'ruta' => 'asistencia.index', 'icono' => 'asistencia', 'roles' => ['administrador', 'instructor']],
            ['nombre' => 'Calificaciones', 'ruta' => 'calificaciones.index', 'icono' => 'notas', 'roles' => ['administrador', 'instructor', 'estudiante']],
            ['nombre' => 'Certificados', 'ruta' => 'certificados.index', 'icono' => 'certificados', 'roles' => ['administrador', 'secretaria', 'estudiante']],
            ['nombre' => 'Pagos', 'ruta' => 'pagos.index', 'icono' => 'pagos', 'roles' => ['administrador', 'secretaria', 'estudiante']],
            ['nombre' => 'Pago QR', 'ruta' => 'pagos.qr.index', 'icono' => 'pago-qr', 'roles' => ['administrador', 'propietario', 'secretaria', 'estudiante']],
            ['nombre' => 'Reportes', 'ruta' => 'reportes.index', 'icono' => 'reportes', 'roles' => ['administrador', 'propietario', 'secretaria']],
            ['nombre' => 'Estadisticas', 'ruta' => 'estadisticas.index', 'icono' => 'estadisticas', 'roles' => ['administrador', 'propietario']],
            ['nombre' => 'Menu dinamico', 'ruta' => 'menus.index', 'icono' => 'menu', 'roles' => ['administrador']],
            ['nombre' => 'Configuracion', 'ruta' => 'configuracion.index', 'icono' => 'configuracion', 'roles' => ['administrador']],
        ];

        foreach ($menuPorRol as $indice => $datosMenu) {
            $menu = Menu::create([
                'nombre' => $datosMenu['nombre'],
                'ruta' => $datosMenu['ruta'],
                'icono' => $datosMenu['icono'],
                'orden' => $indice + 1,
                'activo' => true,
            ]);

            $menu->roles()->attach(
                collect($datosMenu['roles'])->map(fn (string $rol) => $roles[$rol]->id)->all()
            );
        }
    }
}
