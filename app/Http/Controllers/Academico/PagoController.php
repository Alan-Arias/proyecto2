<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Http\Requests\PagoRequest;
use App\Models\Inscripcion;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Services\PagoFacilService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class PagoController extends Controller
{
    public function index(): Response
    {
        $soloLectura = $this->usuarioEsEstudiante();
        $consulta = Pago::query()
            ->with(['inscripcion.estudiante', 'inscripcion.oferta.curso', 'metodoPago']);
        $this->aplicarFiltroEstudiante($consulta);

        return Inertia::render('Academico/Crud', [
            'modulo' => [
                'titulo' => 'Pagos',
                'descripcion' => 'Pagos al contado, por QR y pagos de cuotas.',
                'ruta' => 'pagos',
                'soloLectura' => $soloLectura,
                'campos' => [
                    ['nombre' => 'enrollment_id', 'etiqueta' => 'Inscripcion', 'tipo' => 'select', 'opciones' => $this->opcionesInscripciones()],
                    ['nombre' => 'payment_method_id', 'etiqueta' => 'Metodo de pago', 'tipo' => 'select', 'opciones' => $this->opcionesMetodosPago()],
                    ['nombre' => 'monto', 'etiqueta' => 'Monto pagado', 'tipo' => 'number'],
                    ['nombre' => 'fecha_pago', 'etiqueta' => 'Fecha de pago', 'tipo' => 'date'],
                    ['nombre' => 'referencia', 'etiqueta' => 'Referencia', 'tipo' => 'text'],
                    ['nombre' => 'comprobante', 'etiqueta' => 'Comprobante', 'tipo' => 'text'],
                    ['nombre' => 'observacion', 'etiqueta' => 'Observacion', 'tipo' => 'text'],
                ],
                'columnas' => ['estudianteTexto' => 'Estudiante', 'metodoTexto' => 'Metodo', 'monto' => 'Monto', 'fecha_pago' => 'Fecha', 'estadoPago' => 'Estado pago'],
                'acciones' => [
                    ['etiqueta' => 'Comprobante', 'url' => '/pagos/{id}/comprobante'],
                    ['etiqueta' => 'Ver QR', 'url' => '/pagos/qr/{id}/mostrar'],
                ],
            ],
            'registros' => $consulta
                ->latest()
                ->paginate(10)
                ->through(fn (Pago $pago) => [
                    'id' => $pago->id,
                    'enrollment_id' => $pago->enrollment_id,
                    'payment_method_id' => $pago->payment_method_id,
                    'monto' => $pago->monto,
                    'fecha_pago' => optional($pago->fecha_pago)->toDateString(),
                    'referencia' => $pago->referencia,
                    'comprobante' => $pago->comprobante,
                    'observacion' => $pago->observacion,
                    'estudianteTexto' => $pago->inscripcion?->estudiante?->nombreCompleto(),
                    'metodoTexto' => $pago->metodoPago?->nombre,
                    'estadoPago' => $pago->inscripcion?->estado_pago,
                ]),
        ]);
    }

    public function store(PagoRequest $request, PagoFacilService $pagoFacilService): RedirectResponse
    {
        $this->bloquearGestionEstudiante();

        $datos = $request->validated();
        $metodoPago = MetodoPago::find($datos['payment_method_id']);
        $inscripcion = Inscripcion::findOrFail($datos['enrollment_id']);

        if ($metodoPago?->nombre === 'QR') {
            try {
                $pago = $pagoFacilService->generarQrPago($inscripcion, (float) $datos['monto'], $datos['referencia'] ?? null);
            } catch (Throwable $excepcion) {
                return back()->with('error', 'No se pudo generar el QR en PagoFacil: '.$excepcion->getMessage());
            }

            return redirect()
                ->route('pagos.qr.mostrar', $pago)
                ->with('exito', 'Pago QR generado correctamente.');
        }

        $pago = Pago::create($datos + ['estado' => 'registrado']);
        $pago->inscripcion?->actualizarEstadoPago();

        return back()->with('exito', 'Pago registrado correctamente.');
    }

    public function update(PagoRequest $request, Pago $pago): RedirectResponse
    {
        $this->bloquearGestionEstudiante();

        $inscripcionAnterior = $pago->inscripcion;
        $pago->update($request->validated());
        $inscripcionAnterior?->actualizarEstadoPago();
        $pago->refresh()->inscripcion?->actualizarEstadoPago();

        return back()->with('exito', 'Pago actualizado correctamente.');
    }

    public function destroy(Pago $pago): RedirectResponse
    {
        $this->bloquearGestionEstudiante();

        $inscripcion = $pago->inscripcion;
        $pago->delete();
        $inscripcion?->actualizarEstadoPago();

        return back()->with('exito', 'Pago eliminado correctamente.');
    }

    public function comprobante(Pago $pago): Response
    {
        $pago->load(['inscripcion.estudiante', 'inscripcion.oferta.curso', 'metodoPago']);
        $this->verificarAccesoEstudiante($pago);

        return Inertia::render('Academico/Documento', [
            'tipo' => 'Comprobante de pago',
            'datos' => [
                'estudiante' => $pago->inscripcion?->estudiante?->nombreCompleto(),
                'curso' => $pago->inscripcion?->oferta?->curso?->nombre,
                'metodo' => $pago->metodoPago?->nombre,
                'monto' => $pago->monto,
                'referencia' => $pago->referencia,
                'fecha' => optional($pago->fecha_pago)->format('d/m/Y'),
                'saldoPendiente' => $pago->inscripcion?->saldo_pendiente,
            ],
        ]);
    }

    private function opcionesInscripciones(): array
    {
        $consulta = Inscripcion::query()
            ->with(['estudiante', 'oferta.curso'])
            ->latest();
        $this->aplicarFiltroEstudianteInscripcion($consulta);

        return $consulta
            ->get()
            ->map(fn (Inscripcion $inscripcion) => [
                'valor' => $inscripcion->id,
                'etiqueta' => $inscripcion->estudiante?->nombreCompleto().' - '.$inscripcion->oferta?->curso?->nombre.' | Saldo Bs '.$inscripcion->saldo_pendiente,
            ])
            ->all();
    }

    private function opcionesMetodosPago(): array
    {
        return MetodoPago::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn (MetodoPago $metodoPago) => ['valor' => $metodoPago->id, 'etiqueta' => $metodoPago->nombre])
            ->all();
    }

    private function usuarioEsEstudiante(): bool
    {
        return request()->user()?->tieneRol('estudiante') ?? false;
    }

    private function aplicarFiltroEstudiante($consulta): void
    {
        if ($this->usuarioEsEstudiante()) {
            $consulta->whereHas('inscripcion.estudiante', fn ($consultaEstudiante) => $consultaEstudiante->where('user_id', request()->user()->id));
        }
    }

    private function aplicarFiltroEstudianteInscripcion($consulta): void
    {
        if ($this->usuarioEsEstudiante()) {
            $consulta->whereHas('estudiante', fn ($consultaEstudiante) => $consultaEstudiante->where('user_id', request()->user()->id));
        }
    }

    private function verificarAccesoEstudiante(Pago $pago): void
    {
        if ($this->usuarioEsEstudiante() && $pago->inscripcion?->estudiante?->user_id !== request()->user()->id) {
            abort(403, 'Solo puede ver sus propios pagos.');
        }
    }

    private function bloquearGestionEstudiante(): void
    {
        if ($this->usuarioEsEstudiante()) {
            abort(403, 'El estudiante solo puede consultar sus pagos.');
        }
    }
}
