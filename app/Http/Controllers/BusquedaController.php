<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BusquedaController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $termino = trim((string) $request->query('q', ''));

        if ($request->user()) {
            return Inertia::render('Academico/Busqueda', [
                'termino' => $termino,
                'resultados' => $this->buscarInterno($termino),
            ]);
        }

        return Inertia::render('Publico/Busqueda', [
            'termino' => $termino,
            'resultados' => $this->buscarPublico($termino),
        ]);
    }

    private function buscarInterno(string $termino): array
    {
        if ($termino === '') {
            return [];
        }

        return [
            'estudiantes' => Estudiante::query()
                ->where('nombre', 'like', "%{$termino}%")
                ->orWhere('apellido', 'like', "%{$termino}%")
                ->orWhere('cedula', 'like', "%{$termino}%")
                ->take(8)
                ->get(['id', 'nombre', 'apellido', 'cedula']),
            'cursos' => Curso::query()
                ->where('nombre', 'like', "%{$termino}%")
                ->take(8)
                ->get(['id', 'nombre', 'tipo_licencia', 'estado']),
            'inscripciones' => Inscripcion::query()
                ->with(['estudiante', 'oferta.curso'])
                ->whereHas('estudiante', fn ($consulta) => $consulta
                    ->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('apellido', 'like', "%{$termino}%"))
                ->take(8)
                ->get()
                ->map(fn (Inscripcion $inscripcion) => [
                    'id' => $inscripcion->id,
                    'estudiante' => $inscripcion->estudiante?->nombreCompleto(),
                    'curso' => $inscripcion->oferta?->curso?->nombre,
                    'estadoPago' => $inscripcion->estado_pago,
                ]),
        ];
    }

    private function buscarPublico(string $termino): array
    {
        if ($termino === '') {
            return [];
        }

        return [
            'cursos' => Curso::query()
                ->where('nombre', 'like', "%{$termino}%")
                ->orWhere('tipo_licencia', 'like', "%{$termino}%")
                ->take(8)
                ->get(['id', 'nombre', 'descripcion', 'precio', 'tipo_licencia']),
            'requisitos' => collect([
                'Fotocopia de cedula de identidad vigente.',
                'Inscripcion registrada en secretaria.',
                'Pago al contado, QR o plan de cuotas.',
                'Asistencia a clases teoricas y practicas.',
            ])->filter(fn (string $requisito) => str_contains(strtolower($requisito), strtolower($termino)))->values(),
        ];
    }
}
