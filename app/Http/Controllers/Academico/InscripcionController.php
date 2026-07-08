<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\InscripcionRequest;
use App\Models\Estudiante;
use App\Models\Inscripcion;
use App\Models\MetodoPago;
use App\Models\Oferta;
use App\Models\Pago;
use App\Models\PlanPago;
use App\Services\PagoFacilService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class InscripcionController extends Controller
{
    public function index(): Response
    {
        $soloLectura = $this->usuarioEsEstudiante();
        $consulta = Inscripcion::query()
            ->with(['estudiante', 'oferta.curso']);
        $this->aplicarFiltroEstudiante($consulta);

        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Inscripciones',
                'descripcion' => 'Registro de estudiantes en ofertas y control inicial de pago.',
                'ruta' => 'inscripciones',
                'soloLectura' => $soloLectura,
                'campos' => [
                    ['nombre' => 'student_id', 'etiqueta' => 'Estudiante', 'tipo' => 'select', 'opciones' => $this->opcionesEstudiantes()],
                    ['nombre' => 'offer_id', 'etiqueta' => 'Oferta o curso', 'tipo' => 'select', 'opciones' => $this->opcionesOfertas()],
                    ['nombre' => 'fecha_inscripcion', 'etiqueta' => 'Fecha de inscripcion', 'tipo' => 'date'],
                    ['nombre' => 'forma_pago', 'etiqueta' => 'Forma de pago inicial', 'tipo' => 'select', 'soloCrear' => true, 'opciones' => [
                        ['valor' => 'efectivo', 'etiqueta' => 'Efectivo'],
                        ['valor' => 'qr', 'etiqueta' => 'QR'],
                        ['valor' => 'cuotas', 'etiqueta' => 'Cuotas'],
                    ]],
                    ['nombre' => 'monto_pagado', 'etiqueta' => 'Monto pagado', 'tipo' => 'number', 'soloCrear' => true],
                    ['nombre' => 'referencia', 'etiqueta' => 'Referencia o comprobante', 'tipo' => 'text', 'soloCrear' => true],
                    ['nombre' => 'numero_cuotas', 'etiqueta' => 'Numero de cuotas', 'tipo' => 'number', 'soloCrear' => true, 'mostrarSi' => ['campo' => 'forma_pago', 'valor' => 'cuotas']],
                ],
                'columnas' => ['estudianteTexto' => 'Estudiante', 'cursoTexto' => 'Curso', 'estado_pago' => 'Estado pago', 'saldo_pendiente' => 'Saldo'],
                'acciones' => [
                    ['etiqueta' => 'Constancia', 'url' => '/inscripciones/{id}/constancia'],
                ],
            ],
            'registros' => $consulta
                ->latest()
                ->paginate(10)
                ->through(fn (Inscripcion $inscripcion) => [
                    'id' => $inscripcion->id,
                    'student_id' => $inscripcion->student_id,
                    'offer_id' => $inscripcion->offer_id,
                    'fecha_inscripcion' => optional($inscripcion->fecha_inscripcion)->toDateString(),
                    'estudianteTexto' => $inscripcion->estudiante?->nombreCompleto(),
                    'cursoTexto' => $inscripcion->oferta?->curso?->nombre,
                    'estado_academico' => $inscripcion->estado_academico,
                    'estado_pago' => $inscripcion->estado_pago,
                    'precio_total' => $inscripcion->precio_total,
                    'saldo_pendiente' => $inscripcion->saldo_pendiente,
                ]),
        ]);
    }

    public function store(InscripcionRequest $request): RedirectResponse
    {
        $this->bloquearGestionEstudiante();

        $datos = $request->validated();
        $oferta = Oferta::with('curso')->findOrFail($datos['offer_id']);
        $precioTotal = (float) $oferta->curso->precio;

        $inscripcion = Inscripcion::create([
            'student_id' => $datos['student_id'],
            'offer_id' => $datos['offer_id'],
            'fecha_inscripcion' => $datos['fecha_inscripcion'],
            'estado_academico' => 'inscrito',
            'estado_pago' => 'pendiente',
            'precio_total' => $precioTotal,
            'saldo_pendiente' => $precioTotal,
        ]);

        try {
            $this->registrarPagoInicial($inscripcion, $datos);
        } catch (Throwable $excepcion) {
            $inscripcion->delete();

            return back()->with('error', 'No se pudo registrar el pago inicial: '.$excepcion->getMessage());
        }

        $inscripcion->actualizarEstadoPago();

        return back()->with('exito', 'Inscripcion registrada correctamente.');
    }

    public function update(InscripcionRequest $request, Inscripcion $inscripcion): RedirectResponse
    {
        $this->bloquearGestionEstudiante();

        $datos = $request->validated();
        $oferta = Oferta::with('curso')->findOrFail($datos['offer_id']);

        $inscripcion->update([
            'student_id' => $datos['student_id'],
            'offer_id' => $datos['offer_id'],
            'fecha_inscripcion' => $datos['fecha_inscripcion'],
            'precio_total' => $oferta->curso->precio,
        ]);
        $inscripcion->actualizarEstadoPago();

        return back()->with('exito', 'Inscripcion actualizada correctamente.');
    }

    public function destroy(Inscripcion $inscripcion): RedirectResponse
    {
        $this->bloquearGestionEstudiante();

        $inscripcion->delete();

        return back()->with('exito', 'Inscripcion eliminada correctamente.');
    }

    public function constancia(Inscripcion $inscripcion): Response
    {
        $inscripcion->load(['estudiante', 'oferta.curso', 'oferta.instructor']);
        $this->verificarAccesoEstudiante($inscripcion);

        return Inertia::render('Academico/Documento', [
            'tipo' => 'Constancia de inscripcion',
            'datos' => [
                'estudiante' => $inscripcion->estudiante?->nombreCompleto(),
                'curso' => $inscripcion->oferta?->curso?->nombre,
                'instructor' => $inscripcion->oferta?->instructor?->nombreCompleto(),
                'fecha' => optional($inscripcion->fecha_inscripcion)->format('d/m/Y'),
                'estadoPago' => $inscripcion->estado_pago,
                'saldoPendiente' => $inscripcion->saldo_pendiente,
            ],
        ]);
    }

    private function registrarPagoInicial(Inscripcion $inscripcion, array $datos): void
    {
        if ($datos['forma_pago'] === 'cuotas') {
            $numeroCuotas = (int) ($datos['numero_cuotas'] ?? 2);
            $planPago = PlanPago::create([
                'enrollment_id' => $inscripcion->id,
                'monto_total' => $inscripcion->precio_total,
                'numero_cuotas' => $numeroCuotas,
                'estado' => 'activo',
            ]);

            $montoCuota = round((float) $inscripcion->precio_total / $numeroCuotas, 2);

            for ($numero = 1; $numero <= $numeroCuotas; $numero++) {
                $planPago->cuotas()->create([
                    'numero_cuota' => $numero,
                    'monto' => $montoCuota,
                    'fecha_vencimiento' => now()->addDays($numero * 15)->toDateString(),
                    'estado' => 'pendiente',
                ]);
            }

            return;
        }

        if ($datos['forma_pago'] === 'qr') {
            $montoQr = (float) ($datos['monto_pagado'] ?? $inscripcion->precio_total);
            app(PagoFacilService::class)->generarQrPago(
                $inscripcion,
                min($montoQr, (float) $inscripcion->precio_total),
                $datos['referencia'] ?? null,
            );

            return;
        }

        $nombreMetodo = 'Efectivo';
        $metodoPago = MetodoPago::where('nombre', $nombreMetodo)->first();
        $montoPagado = (float) ($datos['monto_pagado'] ?? $inscripcion->precio_total);

        if ($metodoPago && $montoPagado > 0) {
            Pago::create([
                'enrollment_id' => $inscripcion->id,
                'payment_method_id' => $metodoPago->id,
                'monto' => min($montoPagado, (float) $inscripcion->precio_total),
                'fecha_pago' => now()->toDateString(),
                'referencia' => $datos['referencia'] ?? null,
                'comprobante' => 'Comprobante inicial de inscripcion',
                'estado' => 'registrado',
            ]);
        }
    }

    private function opcionesEstudiantes(): array
    {
        $consulta = Estudiante::query()->orderBy('nombre');

        if ($this->usuarioEsEstudiante()) {
            $consulta->where('user_id', request()->user()->id);
        }

        return $consulta
            ->get()
            ->map(fn (Estudiante $estudiante) => ['valor' => $estudiante->id, 'etiqueta' => $estudiante->nombreCompleto()])
            ->all();
    }

    private function opcionesOfertas(): array
    {
        return Oferta::query()
            ->with('curso')
            ->orderByDesc('fecha_inicio')
            ->get()
            ->map(fn (Oferta $oferta) => [
                'valor' => $oferta->id,
                'etiqueta' => $oferta->codigo.' - '.$oferta->curso?->nombre.' (Bs '.$oferta->curso?->precio.')',
            ])
            ->all();
    }

    private function usuarioEsEstudiante(): bool
    {
        return request()->user()?->tieneRol('estudiante') ?? false;
    }

    private function aplicarFiltroEstudiante($consulta): void
    {
        if ($this->usuarioEsEstudiante()) {
            $consulta->whereHas('estudiante', fn ($consultaEstudiante) => $consultaEstudiante->where('user_id', request()->user()->id));
        }
    }

    private function verificarAccesoEstudiante(Inscripcion $inscripcion): void
    {
        if ($this->usuarioEsEstudiante() && $inscripcion->estudiante?->user_id !== request()->user()->id) {
            abort(403, 'Solo puede ver sus propias inscripciones.');
        }
    }

    private function bloquearGestionEstudiante(): void
    {
        if ($this->usuarioEsEstudiante()) {
            abort(403, 'El estudiante solo puede consultar sus inscripciones.');
        }
    }
}
