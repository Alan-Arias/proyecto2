<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Inscripcion;
use App\Models\Calificacion;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Instructor;
use App\Models\MetodoPago;
use App\Models\Oferta;
use App\Models\Pago;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AutoescuelaFlujoTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_del_administrador_carga_panel_por_rol(): void
    {
        $this->seed();

        $usuario = User::where('email', 'admin@autoescuela.test')->firstOrFail();

        $this->actingAs($usuario)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Index')
                ->where('rolPrincipal', 'administrador')
                ->has('panelRol.tarjetas')
                ->has('estadisticas')
            );
    }

    public function test_secretaria_puede_generar_y_confirmar_pago_qr_academico(): void
    {
        $this->seed();

        $usuario = User::where('email', 'secretaria@autoescuela.test')->firstOrFail();
        $inscripcion = Inscripcion::firstOrFail();

        $this->actingAs($usuario)
            ->post('/pagos/qr/generar', [
                'enrollment_id' => $inscripcion->id,
                'monto' => 50,
                'referencia' => 'QR-PRUEBA-TEST',
            ])
            ->assertRedirect();

        $pago = Pago::where('referencia', 'QR-PRUEBA-TEST')->firstOrFail();

        $this->assertDatabaseHas('transacciones_pago_facil', [
            'pago_id' => $pago->id,
            'estado' => 'pendiente',
        ]);

        $this->actingAs($usuario)
            ->post("/pagos/qr/{$pago->id}/confirmar")
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('transacciones_pago_facil', [
            'pago_id' => $pago->id,
            'estado' => 'confirmado',
        ]);
    }

    public function test_callback_de_pago_facil_confirma_pago_qr(): void
    {
        $this->seed();

        $usuario = User::where('email', 'secretaria@autoescuela.test')->firstOrFail();
        $inscripcion = Inscripcion::firstOrFail();

        $this->actingAs($usuario)
            ->post('/pagos/qr/generar', [
                'enrollment_id' => $inscripcion->id,
                'monto' => 50,
                'referencia' => 'QR-CALLBACK-TEST',
            ])
            ->assertRedirect();

        $pago = Pago::where('referencia', 'QR-CALLBACK-TEST')->firstOrFail();

        $this->postJson('/api/pagofacil/callback', [
            'PedidoID' => $pago->transaccionPagoFacil->codigo_transaccion,
            'Fecha' => now()->toDateString(),
            'Hora' => now()->format('H:i:s'),
            'MetodoPago' => 4,
            'Estado' => 2,
        ])
            ->assertOk()
            ->assertJson([
                'error' => 0,
                'status' => 1,
                'values' => true,
            ]);

        $this->assertDatabaseHas('transacciones_pago_facil', [
            'pago_id' => $pago->id,
            'estado' => 'confirmado',
        ]);
        $this->assertDatabaseHas('payments', [
            'id' => $pago->id,
            'estado' => 'confirmado',
        ]);
    }

    public function test_dashboard_del_estudiante_no_muestra_resumen_general(): void
    {
        $this->seed();

        $usuario = User::where('email', 'estudiante@autoescuela.test')->firstOrFail();

        $this->actingAs($usuario)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Index')
                ->where('rolPrincipal', 'estudiante')
                ->where('mostrarResumenGeneral', false)
                ->where('estadisticas', [])
                ->where('visitasPorPagina', [])
                ->where('ultimasInscripciones', [])
            );
    }

    public function test_secretaria_crea_estudiante_con_usuario_y_rol_estudiante(): void
    {
        $this->seed();

        $secretaria = User::where('email', 'secretaria@autoescuela.test')->firstOrFail();

        $this->actingAs($secretaria)
            ->post('/estudiantes', [
                'nombre' => 'Ana',
                'apellido' => 'Lopez',
                'cedula' => '12345678',
                'email' => 'ana.lopez@autoescuela.test',
                'password' => 'clave123',
                'telefono' => '70001122',
                'direccion' => 'Barrio Central',
                'fecha_nacimiento' => '2004-05-10',
                'estado' => 'activo',
            ])
            ->assertSessionHas('exito');

        $usuario = User::where('email', 'ana.lopez@autoescuela.test')->firstOrFail();

        $this->assertTrue(Hash::check('clave123', $usuario->password));
        $this->assertTrue($usuario->tieneRol('estudiante'));
        $this->assertDatabaseHas('students', [
            'user_id' => $usuario->id,
            'cedula' => '12345678',
        ]);
    }

    public function test_estudiante_solo_ve_su_informacion_academica_y_comercial(): void
    {
        $this->seed();

        $usuario = User::where('email', 'estudiante@autoescuela.test')->firstOrFail();
        $oferta = Oferta::firstOrFail();
        $otroEstudiante = Estudiante::create([
            'nombre' => 'Pedro',
            'apellido' => 'Rojas',
            'cedula' => '5558881',
            'telefono' => '70009988',
            'direccion' => 'Otra zona',
            'fecha_nacimiento' => '2000-01-01',
            'estado' => 'activo',
        ]);
        $otraInscripcion = Inscripcion::create([
            'student_id' => $otroEstudiante->id,
            'offer_id' => $oferta->id,
            'fecha_inscripcion' => now()->toDateString(),
            'estado_academico' => 'aprobado',
            'estado_pago' => 'pagado',
            'precio_total' => 100,
            'saldo_pendiente' => 0,
        ]);
        Calificacion::create([
            'enrollment_id' => $otraInscripcion->id,
            'nota' => 90,
            'estado' => 'aprobado',
        ]);
        Certificado::create([
            'enrollment_id' => $otraInscripcion->id,
            'codigo' => 'CERT-OTRO-001',
            'fecha_emision' => now()->toDateString(),
            'estado' => 'emitido',
        ]);
        Pago::create([
            'enrollment_id' => $otraInscripcion->id,
            'payment_method_id' => MetodoPago::firstOrFail()->id,
            'monto' => 100,
            'fecha_pago' => now()->toDateString(),
            'estado' => 'registrado',
        ]);

        $this->actingAs($usuario)
            ->get('/inscripciones')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('modulo.soloLectura', true)
                ->has('registros.data', 1)
                ->where('registros.data.0.estudianteTexto', 'Lucia Rivera')
            );

        $this->actingAs($usuario)
            ->get('/calificaciones')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('modulo.soloLectura', true)
                ->has('registros.data', 1)
                ->where('registros.data.0.estudianteTexto', 'Lucia Rivera')
            );

        $this->actingAs($usuario)
            ->get('/pagos')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('modulo.soloLectura', true)
                ->has('registros.data', 2)
                ->where('registros.data.0.estudianteTexto', 'Lucia Rivera')
            );

        $this->actingAs($usuario)
            ->get('/certificados')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('soloLectura', true)
                ->has('certificados', 1)
                ->where('certificados.0.estudiante', 'Lucia Rivera')
                ->where('aprobadosSinCertificado', [])
            );
    }

    public function test_instructor_solo_ve_asistencia_de_sus_cursos_asignados(): void
    {
        $this->seed();

        $usuarioInstructor = User::where('email', 'instructor@autoescuela.test')->firstOrFail();
        $rolInstructor = Rol::where('nombre', 'instructor')->firstOrFail();
        $otroUsuarioInstructor = User::create([
            'name' => 'Instructor Externo',
            'email' => 'instructor.externo@autoescuela.test',
            'telefono' => '70008899',
            'password' => Hash::make('password'),
            'activo' => true,
        ]);
        $otroUsuarioInstructor->roles()->attach($rolInstructor);

        $otroInstructor = Instructor::create([
            'user_id' => $otroUsuarioInstructor->id,
            'nombre' => 'Roxana',
            'apellido' => 'Gutierrez',
            'cedula' => '4567899',
            'telefono' => '70007788',
            'especialidad' => 'Practica defensiva',
            'estado' => 'activo',
        ]);
        $otraOferta = Oferta::create([
            'course_id' => Curso::firstOrFail()->id,
            'instructor_id' => $otroInstructor->id,
            'codigo' => 'EXT-2026',
            'fecha_inicio' => now()->addDays(7)->toDateString(),
            'fecha_fin' => now()->addDays(30)->toDateString(),
            'cupos' => 8,
            'estado' => 'activo',
        ]);
        $otroEstudiante = Estudiante::create([
            'nombre' => 'Elena',
            'apellido' => 'Paz',
            'cedula' => '6655443',
            'telefono' => '70005544',
            'direccion' => 'Barrio Norte',
            'fecha_nacimiento' => '2001-03-15',
            'estado' => 'activo',
        ]);
        $otraInscripcion = Inscripcion::create([
            'student_id' => $otroEstudiante->id,
            'offer_id' => $otraOferta->id,
            'fecha_inscripcion' => now()->toDateString(),
            'estado_academico' => 'inscrito',
            'estado_pago' => 'pendiente',
            'precio_total' => 500,
            'saldo_pendiente' => 500,
        ]);
        Asistencia::create([
            'enrollment_id' => $otraInscripcion->id,
            'fecha' => now()->toDateString(),
            'estado' => 'presente',
            'observacion' => 'Registro de otro instructor.',
        ]);

        $this->actingAs($usuarioInstructor)
            ->get('/asistencia')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('registros.data', 1)
                ->where('registros.data.0.estudianteTexto', 'Lucia Rivera')
                ->has('modulo.campos.0.opciones', 1)
                ->where('modulo.campos.0.opciones.0.etiqueta', 'Lucia Rivera - Curso de Conduccion Categoria B')
            );

        $fechaAjena = now()->addDay()->toDateString();

        $this->actingAs($usuarioInstructor)
            ->post('/asistencia', [
                'enrollment_id' => $otraInscripcion->id,
                'fecha' => $fechaAjena,
                'estado' => 'presente',
                'observacion' => 'Intento no permitido.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('attendance', [
            'enrollment_id' => $otraInscripcion->id,
            'fecha' => $fechaAjena,
        ]);
    }

    public function test_instructor_solo_ve_calificaciones_de_sus_cursos_asignados(): void
    {
        $this->seed();

        $usuarioInstructor = User::where('email', 'instructor@autoescuela.test')->firstOrFail();
        $rolInstructor = Rol::where('nombre', 'instructor')->firstOrFail();
        $otroUsuarioInstructor = User::create([
            'name' => 'Instructor Notas',
            'email' => 'instructor.notas@autoescuela.test',
            'telefono' => '70006655',
            'password' => Hash::make('password'),
            'activo' => true,
        ]);
        $otroUsuarioInstructor->roles()->attach($rolInstructor);

        $otroInstructor = Instructor::create([
            'user_id' => $otroUsuarioInstructor->id,
            'nombre' => 'Sofia',
            'apellido' => 'Arias',
            'cedula' => '3322110',
            'telefono' => '70004433',
            'especialidad' => 'Teoria vial',
            'estado' => 'activo',
        ]);
        $otraOferta = Oferta::create([
            'course_id' => Curso::firstOrFail()->id,
            'instructor_id' => $otroInstructor->id,
            'codigo' => 'NOT-2026',
            'fecha_inicio' => now()->addDays(7)->toDateString(),
            'fecha_fin' => now()->addDays(30)->toDateString(),
            'cupos' => 8,
            'estado' => 'activo',
        ]);
        $otroEstudiante = Estudiante::create([
            'nombre' => 'Daniel',
            'apellido' => 'Molina',
            'cedula' => '3344556',
            'telefono' => '70003344',
            'direccion' => 'Zona Este',
            'fecha_nacimiento' => '2002-08-11',
            'estado' => 'activo',
        ]);
        $otraInscripcion = Inscripcion::create([
            'student_id' => $otroEstudiante->id,
            'offer_id' => $otraOferta->id,
            'fecha_inscripcion' => now()->toDateString(),
            'estado_academico' => 'inscrito',
            'estado_pago' => 'pendiente',
            'precio_total' => 500,
            'saldo_pendiente' => 500,
        ]);
        $otraCalificacion = Calificacion::create([
            'enrollment_id' => $otraInscripcion->id,
            'nota' => 88,
            'estado' => 'aprobado',
            'observacion' => 'Nota de otro instructor.',
        ]);

        $this->actingAs($usuarioInstructor)
            ->get('/calificaciones')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('registros.data', 1)
                ->where('registros.data.0.estudianteTexto', 'Lucia Rivera')
                ->has('modulo.campos.0.opciones', 1)
                ->where('modulo.campos.0.opciones.0.etiqueta', 'Lucia Rivera - Curso de Conduccion Categoria B')
            );

        $this->actingAs($usuarioInstructor)
            ->post('/calificaciones', [
                'enrollment_id' => $otraInscripcion->id,
                'nota' => 55,
                'observacion' => 'Intento no permitido.',
            ])
            ->assertForbidden();

        $this->actingAs($usuarioInstructor)
            ->put("/calificaciones/{$otraCalificacion->id}", [
                'enrollment_id' => $otraInscripcion->id,
                'nota' => 70,
                'observacion' => 'Intento de edicion no permitido.',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('grades', [
            'id' => $otraCalificacion->id,
            'nota' => 88,
        ]);
    }

    public function test_secretaria_crea_instructor_sin_vincular_usuario(): void
    {
        $this->seed();

        $secretaria = User::where('email', 'secretaria@autoescuela.test')->firstOrFail();
        $usuarioInstructor = User::where('email', 'instructor@autoescuela.test')->firstOrFail();

        $this->actingAs($secretaria)
            ->post('/instructores', [
                'user_id' => $usuarioInstructor->id,
                'nombre' => 'Ruben',
                'apellido' => 'Salazar',
                'cedula' => '7777001',
                'telefono' => '70001234',
                'especialidad' => 'Practica en ruta',
                'estado' => 'activo',
            ])
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('instructors', [
            'cedula' => '7777001',
            'user_id' => null,
        ]);
    }

    public function test_administrador_vincula_usuario_a_instructor_y_asigna_rol(): void
    {
        $this->seed();

        $administrador = User::where('email', 'admin@autoescuela.test')->firstOrFail();
        $instructor = Instructor::create([
            'nombre' => 'Mario',
            'apellido' => 'Flores',
            'cedula' => '7777002',
            'telefono' => '70004321',
            'especialidad' => 'Practica urbana',
            'estado' => 'activo',
        ]);
        $usuario = User::create([
            'name' => 'Mario Flores',
            'email' => 'mario.flores@autoescuela.test',
            'telefono' => '70004321',
            'password' => Hash::make('password'),
            'activo' => true,
        ]);

        $this->actingAs($administrador)
            ->put("/instructores/{$instructor->id}", [
                'user_id' => $usuario->id,
                'nombre' => 'Mario',
                'apellido' => 'Flores',
                'cedula' => '7777002',
                'telefono' => '70004321',
                'especialidad' => 'Practica urbana',
                'estado' => 'activo',
            ])
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('instructors', [
            'id' => $instructor->id,
            'user_id' => $usuario->id,
        ]);
        $this->assertTrue($usuario->fresh()->tieneRol('instructor'));
        $this->assertDatabaseHas('roles', [
            'nombre' => 'instructor',
        ]);
    }

    public function test_administrador_crea_usuario_desde_instructor_operativo(): void
    {
        $this->seed();

        $administrador = User::where('email', 'admin@autoescuela.test')->firstOrFail();
        $instructor = Instructor::create([
            'nombre' => 'Dante',
            'apellido' => 'Escalante',
            'cedula' => '999999',
            'telefono' => '70000099',
            'especialidad' => 'Especialista',
            'estado' => 'activo',
        ]);
        $rolEstudiante = Rol::where('nombre', 'estudiante')->firstOrFail();

        $this->actingAs($administrador)
            ->get('/usuarios')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('modulo.campos.6.nombre', 'persona_tipo')
                ->where('modulo.campos.6.tipo', 'hidden')
                ->where('modulo.campos.7.nombre', 'persona_id')
                ->where('modulo.campos.7.tipo', 'hidden')
                ->where('registros.data.5.name', 'Dante Escalante')
                ->where('registros.data.5.email', 'Pendiente de correo')
                ->where('registros.data.5.persona_tipo', 'instructor')
                ->where('registros.data.5.persona_id', $instructor->id)
                ->where('registros.data.5.crearUsuarioDesdePersona', true)
            );

        $this->actingAs($administrador)
            ->post('/usuarios', [
                'name' => 'Dante Escalante',
                'email' => 'dante.escalante@autoescuela.test',
                'telefono' => '70000099',
                'password' => 'password',
                'activo' => true,
                'roles' => [$rolEstudiante->id],
                'persona_tipo' => 'instructor',
                'persona_id' => $instructor->id,
            ])
            ->assertSessionHas('exito');

        $usuario = User::where('email', 'dante.escalante@autoescuela.test')->firstOrFail();

        $this->assertTrue($usuario->tieneRol('instructor'));
        $this->assertDatabaseHas('instructors', [
            'id' => $instructor->id,
            'user_id' => $usuario->id,
        ]);
    }
}
