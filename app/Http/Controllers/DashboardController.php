<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Calificacion;
use App\Models\Certificado;
use App\Models\Configuracion;
use App\Models\Curso;
use App\Models\CuotaPago;
use App\Models\Estudiante;
use App\Models\Instructor;
use App\Models\Inscripcion;
use App\Models\Menu;
use App\Models\Pago;
use App\Models\Rol;
use App\Models\TransaccionPagoFacil;
use App\Models\User;
use App\Models\VisitaPagina;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $usuario = request()->user();
        $rolPrincipal = $this->obtenerRolPrincipal($usuario);
        $mostrarResumenGeneral = $rolPrincipal === 'administrador';

        return Inertia::render('Dashboard/Index', [
            'rolPrincipal' => $rolPrincipal,
            'mostrarResumenGeneral' => $mostrarResumenGeneral,
            'panelRol' => $this->prepararPanelPorRol($rolPrincipal, $usuario),
            'estadisticas' => $mostrarResumenGeneral ? [
                'totalEstudiantes' => Estudiante::count(),
                'totalCursos' => Curso::count(),
                'totalInscripciones' => Inscripcion::count(),
                'totalPagos' => Pago::count(),
                'ingresos' => Pago::sum('monto'),
                'estudiantesAprobados' => Inscripcion::whereHas('calificacion', fn ($consulta) => $consulta->where('estado', 'aprobado'))->count(),
                'estudiantesReprobados' => Inscripcion::whereHas('calificacion', fn ($consulta) => $consulta->where('estado', 'reprobado'))->count(),
                'cursosActivos' => Curso::where('estado', 'activo')->count(),
            ] : [],
            'visitasPorPagina' => $mostrarResumenGeneral ? VisitaPagina::query()
                ->orderByDesc('visitas')
                ->take(8)
                ->get(['pagina', 'visitas']) : [],
            'ultimasInscripciones' => $mostrarResumenGeneral ? Inscripcion::query()
                ->with(['estudiante', 'oferta.curso'])
                ->latest()
                ->take(6)
                ->get()
                ->map(fn (Inscripcion $inscripcion) => [
                    'id' => $inscripcion->id,
                    'estudiante' => $inscripcion->estudiante?->nombreCompleto(),
                    'curso' => $inscripcion->oferta?->curso?->nombre,
                    'estadoPago' => $inscripcion->estado_pago,
                    'saldoPendiente' => $inscripcion->saldo_pendiente,
                ]) : [],
        ]);
    }

    private function obtenerRolPrincipal(User $usuario): string
    {
        $prioridad = ['administrador', 'propietario', 'secretaria', 'instructor', 'estudiante'];
        $rolesUsuario = $usuario->roles->pluck('nombre');

        return collect($prioridad)->first(fn (string $rol) => $rolesUsuario->contains($rol)) ?? 'estudiante';
    }

    private function prepararPanelPorRol(string $rolPrincipal, User $usuario): array
    {
        return match ($rolPrincipal) {
            'administrador' => $this->panelAdministrador(),
            'propietario' => $this->panelPropietario(),
            'secretaria' => $this->panelSecretaria(),
            'instructor' => $this->panelInstructor($usuario),
            'estudiante' => $this->panelEstudiante($usuario),
            default => $this->panelSecretaria(),
        };
    }

    private function panelAdministrador(): array
    {
        return [
            'titulo' => 'Dashboard del administrador',
            'descripcion' => 'Gestion tecnica de usuarios, roles, menu dinamico y configuracion.',
            'tarjetas' => [
                $this->tarjeta('Usuarios', User::count(), 'Registrados'),
                $this->tarjeta('Usuarios activos', User::where('activo', true)->count(), 'Con acceso habilitado'),
                $this->tarjeta('Roles', Rol::count(), 'Perfiles disponibles'),
                $this->tarjeta('Opciones de menu', Menu::count(), 'Menu dinamico'),
                $this->tarjeta('Configuraciones', Configuracion::count(), 'Parametros basicos'),
            ],
            'acciones' => [
                $this->accion('Usuarios', '/usuarios'),
                $this->accion('Roles', '/roles'),
                $this->accion('Menu dinamico', '/menus'),
                $this->accion('Configuracion', '/configuracion'),
            ],
            'listas' => [
                $this->listaVisitas(),
            ],
        ];
    }

    private function panelPropietario(): array
    {
        return [
            'titulo' => 'Dashboard del propietario',
            'descripcion' => 'Resumen comercial, ingresos, demanda de cursos y pagos pendientes.',
            'tarjetas' => [
                $this->tarjeta('Estudiantes inscritos', Inscripcion::count(), 'Historial total'),
                $this->tarjeta('Cursos activos', Curso::where('estado', 'activo')->count(), 'Disponibles para seguimiento'),
                $this->tarjeta('Ingresos registrados', 'Bs '.Pago::whereIn('estado', ['registrado', 'confirmado'])->sum('monto'), 'Pagos confirmados'),
                $this->tarjeta('Pagos pendientes', Inscripcion::where('estado_pago', '!=', 'pagado')->count(), 'Inscripciones con saldo'),
                $this->tarjeta('Aprobados', Calificacion::where('estado', 'aprobado')->count(), 'Nota mayor o igual a 51'),
                $this->tarjeta('Reprobados', Calificacion::where('estado', 'reprobado')->count(), 'Nota menor a 51'),
            ],
            'acciones' => [
                $this->accion('Reportes', '/reportes'),
                $this->accion('Estadisticas', '/estadisticas'),
                $this->accion('Pagos', '/pagos'),
                $this->accion('Cursos', '/cursos'),
            ],
            'listas' => [
                [
                    'titulo' => 'Cursos mas solicitados',
                    'items' => $this->cursosMasSolicitados(),
                ],
                $this->listaVisitas(),
            ],
        ];
    }

    private function panelSecretaria(): array
    {
        return [
            'titulo' => 'Dashboard de la secretaria',
            'descripcion' => 'Operacion diaria de estudiantes, inscripciones, pagos y constancias.',
            'tarjetas' => [
                $this->tarjeta('Estudiantes activos', Estudiante::where('estado', 'activo')->count(), 'Disponibles para inscribir'),
                $this->tarjeta('Cursos disponibles', Curso::whereIn('estado', ['disponible', 'activo'])->count(), 'Oferta academica'),
                $this->tarjeta('Pagos pendientes', Inscripcion::where('estado_pago', '!=', 'pagado')->count(), 'Saldos por cobrar'),
                $this->tarjeta('QR pendientes', TransaccionPagoFacil::whereIn('estado', ['generado', 'pendiente'])->count(), 'PagoFacil academico'),
                $this->tarjeta('Cuotas pendientes', CuotaPago::where('estado', 'pendiente')->count(), 'Planes de pago'),
                $this->tarjeta('Constancias', Inscripcion::count(), 'Inscripciones emitibles'),
            ],
            'acciones' => [
                $this->accion('Estudiantes', '/estudiantes'),
                $this->accion('Inscripciones', '/inscripciones'),
                $this->accion('Pagos', '/pagos'),
                $this->accion('Pago QR', '/pagos/qr'),
                $this->accion('Cursos', '/cursos'),
            ],
            'listas' => [
                [
                    'titulo' => 'Inscripciones recientes',
                    'items' => Inscripcion::query()
                        ->with(['estudiante', 'oferta.curso'])
                        ->latest()
                        ->take(5)
                        ->get()
                        ->map(fn (Inscripcion $inscripcion) => [
                            'nombre' => $inscripcion->estudiante?->nombreCompleto(),
                            'detalle' => $inscripcion->oferta?->curso?->nombre.' | '.$inscripcion->estado_pago,
                        ]),
                ],
            ],
        ];
    }

    private function panelInstructor(User $usuario): array
    {
        $instructor = Instructor::where('user_id', $usuario->id)->first();
        $ofertasIds = $instructor
            ? $instructor->ofertas()->pluck('id')
            : collect();
        $inscripciones = Inscripcion::whereIn('offer_id', $ofertasIds);
        $totalAsistencias = Asistencia::whereHas('inscripcion', fn ($consulta) => $consulta->whereIn('offer_id', $ofertasIds))->count();
        $presentes = Asistencia::whereHas('inscripcion', fn ($consulta) => $consulta->whereIn('offer_id', $ofertasIds))
            ->where('estado', 'presente')
            ->count();
        $promedioAsistencia = $totalAsistencias > 0 ? round(($presentes / $totalAsistencias) * 100, 1).'%' : '0%';

        return [
            'titulo' => 'Dashboard del instructor',
            'descripcion' => 'Seguimiento academico de cursos asignados, asistencia y calificaciones.',
            'tarjetas' => [
                $this->tarjeta('Cursos asignados', $ofertasIds->count(), 'Ofertas activas o historicas'),
                $this->tarjeta('Estudiantes inscritos', (clone $inscripciones)->count(), 'En sus cursos'),
                $this->tarjeta('Asistencias registradas', $totalAsistencias, 'Clases tomadas'),
                $this->tarjeta('Notas pendientes', (clone $inscripciones)->whereDoesntHave('calificacion')->count(), 'Por registrar'),
                $this->tarjeta('Aprobados', (clone $inscripciones)->whereHas('calificacion', fn ($consulta) => $consulta->where('estado', 'aprobado'))->count(), 'Con nota suficiente'),
                $this->tarjeta('Promedio asistencia', $promedioAsistencia, 'Sobre registros existentes'),
            ],
            'acciones' => [
                $this->accion('Asistencia', '/asistencia'),
                $this->accion('Calificaciones', '/calificaciones'),
            ],
            'listas' => [
                [
                    'titulo' => 'Estudiantes de mis cursos',
                    'items' => Inscripcion::query()
                        ->with(['estudiante', 'oferta.curso'])
                        ->whereIn('offer_id', $ofertasIds)
                        ->latest()
                        ->take(5)
                        ->get()
                        ->map(fn (Inscripcion $inscripcion) => [
                            'nombre' => $inscripcion->estudiante?->nombreCompleto(),
                            'detalle' => $inscripcion->oferta?->curso?->nombre.' | '.$inscripcion->estado_academico,
                        ]),
                ],
            ],
        ];
    }

    private function panelEstudiante(User $usuario): array
    {
        $estudiante = Estudiante::where('user_id', $usuario->id)->first();
        $inscripciones = $estudiante
            ? $estudiante->inscripciones()->with(['oferta.curso', 'calificacion', 'certificado', 'pagos.transaccionPagoFacil'])->get()
            : collect();

        return [
            'titulo' => 'Dashboard del estudiante',
            'descripcion' => 'Consulta de cursos inscritos, asistencia, notas, pagos y certificados.',
            'tarjetas' => [
                $this->tarjeta('Cursos inscritos', $inscripciones->count(), 'Historial academico'),
                $this->tarjeta('Notas registradas', $inscripciones->whereNotNull('calificacion')->count(), 'Calificaciones visibles'),
                $this->tarjeta('Pagos pendientes', $inscripciones->where('estado_pago', '!=', 'pagado')->count(), 'Con saldo pendiente'),
                $this->tarjeta('Certificados', $inscripciones->whereNotNull('certificado')->count(), 'Disponibles'),
                $this->tarjeta('QR pendientes', $this->contarQrPendientes($inscripciones), 'PagoFacil academico'),
            ],
            'acciones' => [
                $this->accion('Mis inscripciones', '/inscripciones'),
                $this->accion('Pagos', '/pagos'),
                $this->accion('Certificados', '/certificados'),
            ],
            'listas' => [
                [
                    'titulo' => 'Mis cursos',
                    'items' => $inscripciones
                        ->take(5)
                        ->map(fn (Inscripcion $inscripcion) => [
                            'nombre' => $inscripcion->oferta?->curso?->nombre,
                            'detalle' => 'Pago: '.$inscripcion->estado_pago.' | Nota: '.($inscripcion->calificacion?->nota ?? 'pendiente'),
                        ]),
                ],
            ],
        ];
    }

    private function tarjeta(string $titulo, int|float|string $valor, string $descripcion): array
    {
        return compact('titulo', 'valor', 'descripcion');
    }

    private function accion(string $etiqueta, string $url): array
    {
        return compact('etiqueta', 'url');
    }

    private function listaVisitas(): array
    {
        return [
            'titulo' => 'Visitas por pagina',
            'items' => VisitaPagina::query()
                ->orderByDesc('visitas')
                ->take(5)
                ->get()
                ->map(fn (VisitaPagina $visitaPagina) => [
                    'nombre' => $visitaPagina->pagina,
                    'detalle' => $visitaPagina->visitas.' visitas',
                ]),
        ];
    }

    private function cursosMasSolicitados(): Collection
    {
        return Curso::query()
            ->with('ofertas.inscripciones')
            ->get()
            ->map(fn (Curso $curso) => [
                'nombre' => $curso->nombre,
                'detalle' => $curso->ofertas->sum(fn ($oferta) => $oferta->inscripciones->count()).' inscripciones',
            ])
            ->sortByDesc('detalle')
            ->take(5)
            ->values();
    }

    private function contarQrPendientes(Collection $inscripciones): int
    {
        return $inscripciones
            ->flatMap(fn (Inscripcion $inscripcion) => $inscripcion->pagos)
            ->filter(fn (Pago $pago) => in_array($pago->transaccionPagoFacil?->estado, ['generado', 'pendiente'], true))
            ->count();
    }
}
