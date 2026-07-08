<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use App\Models\Inscripcion;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CertificadoController extends Controller
{
    public function index(): Response
    {
        $soloLectura = $this->usuarioEsEstudiante();
        $consultaCertificados = Certificado::query()
            ->with(['inscripcion.estudiante', 'inscripcion.oferta.curso', 'inscripcion.oferta.instructor', 'inscripcion.calificacion']);
        $this->aplicarFiltroEstudianteCertificado($consultaCertificados);

        $consultaAprobados = Inscripcion::query()
            ->with(['estudiante', 'oferta.curso', 'calificacion'])
            ->whereDoesntHave('certificado')
            ->whereHas('calificacion', fn ($consulta) => $consulta->where('nota', '>=', 51));

        return Inertia::render('Academico/Certificados', [
            'soloLectura' => $soloLectura,
            'certificados' => $consultaCertificados
                ->latest()
                ->get()
                ->map(fn (Certificado $certificado) => [
                    'id' => $certificado->id,
                    'codigo' => $certificado->codigo,
                    'fecha' => optional($certificado->fecha_emision)->format('d/m/Y'),
                    'estudiante' => $certificado->inscripcion?->estudiante?->nombreCompleto(),
                    'curso' => $certificado->inscripcion?->oferta?->curso?->nombre,
                    'instructor' => $certificado->inscripcion?->oferta?->instructor?->nombreCompleto(),
                    'nota' => $certificado->inscripcion?->calificacion?->nota,
                ]),
            'aprobadosSinCertificado' => $soloLectura ? [] : $consultaAprobados
                ->get()
                ->map(fn (Inscripcion $inscripcion) => [
                    'id' => $inscripcion->id,
                    'estudiante' => $inscripcion->estudiante?->nombreCompleto(),
                    'curso' => $inscripcion->oferta?->curso?->nombre,
                    'nota' => $inscripcion->calificacion?->nota,
                ]),
        ]);
    }

    public function generar(Inscripcion $inscripcion): RedirectResponse
    {
        $inscripcion->load(['calificacion']);

        if (! $inscripcion->estaAprobada()) {
            return back()->with('error', 'Solo se puede generar certificado para estudiantes aprobados.');
        }

        Certificado::firstOrCreate(
            ['enrollment_id' => $inscripcion->id],
            [
                'codigo' => 'CERT-'.now()->format('Ymd').'-'.str_pad((string) $inscripcion->id, 4, '0', STR_PAD_LEFT),
                'fecha_emision' => now()->toDateString(),
                'estado' => 'emitido',
            ]
        );

        return back()->with('exito', 'Certificado generado correctamente.');
    }

    public function ver(Certificado $certificado): Response
    {
        $certificado->load(['inscripcion.estudiante', 'inscripcion.oferta.curso', 'inscripcion.oferta.instructor', 'inscripcion.calificacion']);
        $this->verificarAccesoEstudiante($certificado);

        return Inertia::render('Academico/Documento', [
            'tipo' => 'Certificado academico',
            'datos' => [
                'codigo' => $certificado->codigo,
                'estudiante' => $certificado->inscripcion?->estudiante?->nombreCompleto(),
                'curso' => $certificado->inscripcion?->oferta?->curso?->nombre,
                'instructor' => $certificado->inscripcion?->oferta?->instructor?->nombreCompleto(),
                'nota' => $certificado->inscripcion?->calificacion?->nota,
                'fecha' => optional($certificado->fecha_emision)->format('d/m/Y'),
            ],
        ]);
    }

    private function usuarioEsEstudiante(): bool
    {
        return request()->user()?->tieneRol('estudiante') ?? false;
    }

    private function aplicarFiltroEstudianteCertificado($consulta): void
    {
        if ($this->usuarioEsEstudiante()) {
            $consulta->whereHas('inscripcion.estudiante', fn ($consultaEstudiante) => $consultaEstudiante->where('user_id', request()->user()->id));
        }
    }

    private function verificarAccesoEstudiante(Certificado $certificado): void
    {
        if ($this->usuarioEsEstudiante() && $certificado->inscripcion?->estudiante?->user_id !== request()->user()->id) {
            abort(403, 'Solo puede ver sus propios certificados.');
        }
    }
}
