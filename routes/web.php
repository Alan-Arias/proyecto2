<?php

use App\Http\Controllers\Academico\AsistenciaController;
use App\Http\Controllers\Academico\CalificacionController;
use App\Http\Controllers\Academico\CertificadoController;
use App\Http\Controllers\Academico\ConfiguracionController;
use App\Http\Controllers\Academico\CursoController;
use App\Http\Controllers\Academico\EstadisticaController;
use App\Http\Controllers\Academico\EstudianteController;
use App\Http\Controllers\Academico\InstructorController;
use App\Http\Controllers\Academico\InscripcionController;
use App\Http\Controllers\Academico\MateriaController;
use App\Http\Controllers\Academico\MenuController;
use App\Http\Controllers\Academico\OfertaController;
use App\Http\Controllers\Academico\PagoController;
use App\Http\Controllers\Academico\PagoFacilController;
use App\Http\Controllers\Academico\ReporteController;
use App\Http\Controllers\Academico\RolController;
use App\Http\Controllers\Academico\UsuarioController;
use App\Http\Controllers\AutenticacionController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Publico\PaginaPublicaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PaginaPublicaController::class, 'inicio'])->name('inicio');
Route::get('/cursos-publicos', [PaginaPublicaController::class, 'cursos'])->name('publico.cursos');
Route::get('/licencias', [PaginaPublicaController::class, 'licencias'])->name('publico.licencias');
Route::get('/requisitos', [PaginaPublicaController::class, 'requisitos'])->name('publico.requisitos');
Route::get('/contacto', [PaginaPublicaController::class, 'contacto'])->name('publico.contacto');
Route::get('/ubicacion', [PaginaPublicaController::class, 'ubicacion'])->name('publico.ubicacion');
Route::get('/buscar', BusquedaController::class)->name('buscar');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AutenticacionController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AutenticacionController::class, 'iniciarSesion'])->name('login.iniciar');
});

Route::post('/logout', [AutenticacionController::class, 'cerrarSesion'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('usuarios', UsuarioController::class)
        ->except(['show'])
        ->parameters(['usuarios' => 'usuario'])
        ->middleware('rol:administrador');

    Route::resource('roles', RolController::class)
        ->except(['show'])
        ->middleware('rol:administrador');

    Route::resource('menus', MenuController::class)
        ->except(['show'])
        ->parameters(['menus' => 'menu'])
        ->middleware('rol:administrador');

    Route::resource('estudiantes', EstudianteController::class)
        ->except(['show'])
        ->parameters(['estudiantes' => 'estudiante'])
        ->middleware('rol:administrador,secretaria');

    Route::resource('instructores', InstructorController::class)
        ->except(['show'])
        ->parameters(['instructores' => 'instructor'])
        ->middleware('rol:administrador,secretaria');

    Route::resource('materias', MateriaController::class)
        ->except(['show'])
        ->parameters(['materias' => 'materia'])
        ->middleware('rol:administrador,secretaria');

    Route::resource('cursos', CursoController::class)
        ->except(['show'])
        ->parameters(['cursos' => 'curso'])
        ->middleware('rol:administrador,secretaria,propietario');

    Route::resource('ofertas', OfertaController::class)
        ->except(['show'])
        ->parameters(['ofertas' => 'oferta'])
        ->middleware('rol:administrador,secretaria');

    Route::resource('inscripciones', InscripcionController::class)
        ->except(['show'])
        ->parameters(['inscripciones' => 'inscripcion'])
        ->middleware('rol:administrador,secretaria,estudiante');
    Route::get('/inscripciones/{inscripcion}/constancia', [InscripcionController::class, 'constancia'])
        ->name('inscripciones.constancia')
        ->middleware('rol:administrador,secretaria,estudiante');

    Route::resource('asistencia', AsistenciaController::class)
        ->except(['show'])
        ->parameters(['asistencia' => 'asistencia'])
        ->middleware('rol:administrador,instructor');

    Route::resource('calificaciones', CalificacionController::class)
        ->except(['show'])
        ->parameters(['calificaciones' => 'calificacion'])
        ->middleware('rol:administrador,instructor,estudiante');

    Route::get('/certificados', [CertificadoController::class, 'index'])
        ->name('certificados.index')
        ->middleware('rol:administrador,secretaria,estudiante');
    Route::post('/certificados/{inscripcion}/generar', [CertificadoController::class, 'generar'])
        ->name('certificados.generar')
        ->middleware('rol:administrador,secretaria');
    Route::get('/certificados/{certificado}', [CertificadoController::class, 'ver'])
        ->name('certificados.ver')
        ->middleware('rol:administrador,secretaria,estudiante');

    Route::resource('pagos', PagoController::class)
        ->except(['show'])
        ->parameters(['pagos' => 'pago'])
        ->middleware('rol:administrador,secretaria,estudiante');
    Route::get('/pagos/{pago}/comprobante', [PagoController::class, 'comprobante'])
        ->name('pagos.comprobante')
        ->middleware('rol:administrador,secretaria,estudiante');
    Route::get('/pagos/qr', [PagoFacilController::class, 'index'])
        ->name('pagos.qr.index')
        ->middleware('rol:administrador,secretaria,propietario,estudiante');
    Route::post('/pagos/qr/generar', [PagoFacilController::class, 'generarPagoQr'])
        ->name('pagos.qr.generar')
        ->middleware('rol:administrador,secretaria,propietario');
    Route::get('/pagos/qr/{pago}/mostrar', [PagoFacilController::class, 'mostrarQr'])
        ->name('pagos.qr.mostrar')
        ->middleware('rol:administrador,secretaria,propietario,estudiante');
    Route::post('/pagos/qr/{pago}/consultar', [PagoFacilController::class, 'consultarPago'])
        ->name('pagos.qr.consultar')
        ->middleware('rol:administrador,secretaria,propietario,estudiante');
    Route::post('/pagos/qr/{pago}/confirmar', [PagoFacilController::class, 'confirmarPago'])
        ->name('pagos.qr.confirmar')
        ->middleware('rol:administrador,secretaria,propietario');
    Route::post('/pagos/qr/{pago}/cancelar', [PagoFacilController::class, 'cancelarPago'])
        ->name('pagos.qr.cancelar')
        ->middleware('rol:administrador,secretaria,propietario');

    Route::get('/reportes', [ReporteController::class, 'index'])
        ->name('reportes.index')
        ->middleware('rol:administrador,propietario,secretaria');

    Route::get('/estadisticas', [EstadisticaController::class, 'index'])
        ->name('estadisticas.index')
        ->middleware('rol:administrador,propietario');

    Route::resource('configuracion', ConfiguracionController::class)
        ->except(['show'])
        ->parameters(['configuracion' => 'configuracion'])
        ->middleware('rol:administrador');
});
