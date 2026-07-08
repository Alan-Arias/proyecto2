<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\VisitaPagina;
use Inertia\Inertia;
use Inertia\Response;

class ReporteController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Academico/Reportes', [
            'estudiantesInscritos' => Inscripcion::query()
                ->with(['estudiante', 'oferta.curso'])
                ->latest()
                ->get()
                ->map(fn (Inscripcion $inscripcion) => [
                    'estudiante' => $inscripcion->estudiante?->nombreCompleto(),
                    'curso' => $inscripcion->oferta?->curso?->nombre,
                    'estadoPago' => $inscripcion->estado_pago,
                    'saldo' => $inscripcion->saldo_pendiente,
                ]),
            'pagosRecibidos' => Pago::query()
                ->with(['inscripcion.estudiante', 'metodoPago'])
                ->latest()
                ->get()
                ->map(fn (Pago $pago) => [
                    'estudiante' => $pago->inscripcion?->estudiante?->nombreCompleto(),
                    'metodo' => $pago->metodoPago?->nombre,
                    'monto' => $pago->monto,
                    'fecha' => optional($pago->fecha_pago)->format('d/m/Y'),
                ]),
            'cursosActivos' => Curso::query()
                ->whereIn('estado', ['activo', 'disponible'])
                ->get(['nombre', 'tipo_licencia', 'precio', 'estado']),
            'aprobados' => Inscripcion::query()
                ->with(['estudiante', 'oferta.curso', 'calificacion'])
                ->whereHas('calificacion', fn ($consulta) => $consulta->where('estado', 'aprobado'))
                ->get()
                ->map(fn (Inscripcion $inscripcion) => [
                    'estudiante' => $inscripcion->estudiante?->nombreCompleto(),
                    'curso' => $inscripcion->oferta?->curso?->nombre,
                    'nota' => $inscripcion->calificacion?->nota,
                ]),
            'reprobados' => Inscripcion::query()
                ->with(['estudiante', 'oferta.curso', 'calificacion'])
                ->whereHas('calificacion', fn ($consulta) => $consulta->where('estado', 'reprobado'))
                ->get()
                ->map(fn (Inscripcion $inscripcion) => [
                    'estudiante' => $inscripcion->estudiante?->nombreCompleto(),
                    'curso' => $inscripcion->oferta?->curso?->nombre,
                    'nota' => $inscripcion->calificacion?->nota,
                ]),
            'visitas' => VisitaPagina::query()->orderByDesc('visitas')->get(['pagina', 'visitas']),
        ]);
    }
}
