<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\VisitaPagina;
use Inertia\Inertia;
use Inertia\Response;

class EstadisticaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Academico/Estadisticas', [
            'tarjetas' => [
                ['titulo' => 'Total estudiantes', 'valor' => Estudiante::count()],
                ['titulo' => 'Total cursos', 'valor' => Curso::count()],
                ['titulo' => 'Total inscripciones', 'valor' => Inscripcion::count()],
                ['titulo' => 'Total pagos', 'valor' => Pago::count()],
                ['titulo' => 'Ingresos registrados', 'valor' => 'Bs '.number_format((float) Pago::sum('monto'), 2)],
                ['titulo' => 'Estudiantes aprobados', 'valor' => Inscripcion::whereHas('calificacion', fn ($consulta) => $consulta->where('estado', 'aprobado'))->count()],
                ['titulo' => 'Estudiantes reprobados', 'valor' => Inscripcion::whereHas('calificacion', fn ($consulta) => $consulta->where('estado', 'reprobado'))->count()],
                ['titulo' => 'Cursos activos', 'valor' => Curso::where('estado', 'activo')->count()],
            ],
            'visitas' => VisitaPagina::query()->orderByDesc('visitas')->get(['pagina', 'visitas']),
        ]);
    }
}
