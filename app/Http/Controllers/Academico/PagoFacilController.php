<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Services\PagoFacilService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class PagoFacilController extends Controller
{
    public function index(): Response
    {
        $consulta = Pago::query()
            ->with(['inscripcion.estudiante', 'inscripcion.oferta.curso', 'transaccionPagoFacil'])
            ->whereHas('transaccionPagoFacil');

        if (request()->user()?->tieneRol('estudiante')) {
            $consulta->whereHas('inscripcion.estudiante', function ($consultaEstudiante) {
                $consultaEstudiante->where('user_id', request()->user()->id);
            });
        }

        return Inertia::render('Academico/PagoQrListado', [
            'pagosQr' => $consulta
                ->latest()
                ->paginate(10)
                ->through(fn (Pago $pago) => [
                    'id' => $pago->id,
                    'estudiante' => $pago->inscripcion?->estudiante?->nombreCompleto(),
                    'curso' => $pago->inscripcion?->oferta?->curso?->nombre,
                    'monto' => $pago->monto,
                    'estado' => $pago->transaccionPagoFacil?->estado,
                    'codigo' => $pago->transaccionPagoFacil?->codigo_transaccion,
                ]),
        ]);
    }

    public function generarPagoQr(Request $request, PagoFacilService $pagoFacilService): RedirectResponse
    {
        $datos = $request->validate([
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'monto' => ['required', 'numeric', 'min:1'],
            'referencia' => ['nullable', 'string', 'max:120'],
        ], [
            'enrollment_id.required' => 'Debe seleccionar una inscripcion valida.',
            'monto.required' => 'El monto del pago debe ser mayor a cero.',
            'monto.min' => 'El monto del pago debe ser mayor a cero.',
        ]);

        $inscripcion = Inscripcion::with(['estudiante', 'oferta.curso'])->findOrFail($datos['enrollment_id']);

        if ((float) $inscripcion->saldo_pendiente <= 0) {
            return back()->with('error', 'No se puede generar QR porque la inscripcion ya esta pagada.');
        }

        if ((float) $datos['monto'] > (float) $inscripcion->saldo_pendiente) {
            return back()->with('error', 'El monto pagado no puede ser mayor al saldo pendiente.');
        }

        try {
            $pago = $pagoFacilService->generarQrPago($inscripcion, (float) $datos['monto'], $datos['referencia'] ?? null);
        } catch (Throwable $excepcion) {
            return back()->with('error', 'No se pudo generar el QR en PagoFacil: '.$excepcion->getMessage());
        }

        return redirect()
            ->route('pagos.qr.mostrar', $pago)
            ->with('exito', 'Pago QR generado correctamente.');
    }

    public function mostrarQr(Pago $pago): Response|RedirectResponse
    {
        $pago->load(['inscripcion.estudiante.usuario', 'inscripcion.oferta.curso', 'transaccionPagoFacil']);
        $this->verificarAccesoEstudiante($pago);

        if (! $pago->transaccionPagoFacil) {
            return redirect()->route('pagos.index')->with('error', 'Este pago no tiene una transaccion QR de PagoFacil.');
        }

        return Inertia::render('Academico/PagoQr', [
            'pagoQr' => $this->prepararDatosQr($pago),
        ]);
    }

    public function consultarPago(Pago $pago, PagoFacilService $pagoFacilService): RedirectResponse
    {
        $pago->load(['inscripcion.estudiante.usuario', 'transaccionPagoFacil']);
        $this->verificarAccesoEstudiante($pago);

        try {
            $transaccion = $pagoFacilService->consultarEstadoPago($pago);
        } catch (Throwable $excepcion) {
            return back()->with('error', 'No se pudo consultar PagoFacil: '.$excepcion->getMessage());
        }

        return back()->with('exito', 'Estado actual del pago QR: '.$transaccion->estado.'.');
    }

    public function confirmarPago(Pago $pago, PagoFacilService $pagoFacilService): RedirectResponse
    {
        $pago->load('transaccionPagoFacil');

        if (! $pago->transaccionPagoFacil) {
            return back()->with('error', 'No se pudo confirmar el pago porque no existe transaccion QR.');
        }

        try {
            $transaccion = $pagoFacilService->confirmarTransaccionAcademica($pago);
        } catch (Throwable $excepcion) {
            return back()->with('error', 'No se pudo confirmar el pago QR: '.$excepcion->getMessage());
        }

        if ($transaccion?->estado !== 'confirmado') {
            return back()->with('error', 'PagoFacil aun reporta el QR como '.$transaccion?->estado.'.');
        }

        return back()->with('exito', 'El pago QR fue confirmado correctamente.');
    }

    public function cancelarPago(Pago $pago, PagoFacilService $pagoFacilService): RedirectResponse
    {
        $pago->load('transaccionPagoFacil');

        if ($pago->transaccionPagoFacil?->estado === 'confirmado') {
            return back()->with('error', 'No se puede cancelar un pago QR confirmado.');
        }

        $pagoFacilService->cancelarTransaccionAcademica($pago);

        return back()->with('exito', 'El pago QR fue cancelado correctamente.');
    }

    public function callback(Request $request, PagoFacilService $pagoFacilService): JsonResponse
    {
        $transaccion = $pagoFacilService->procesarCallback($request->all());

        if (! $transaccion) {
            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => 'Transaccion PagoFacil no encontrada.',
                'values' => false,
            ], 404);
        }

        return response()->json([
            'error' => 0,
            'status' => 1,
            'message' => 'Notificacion PagoFacil procesada correctamente.',
            'values' => true,
        ]);
    }

    private function prepararDatosQr(Pago $pago): array
    {
        $transaccion = $pago->transaccionPagoFacil;

        return [
            'id' => $pago->id,
            'estudiante' => $pago->inscripcion?->estudiante?->nombreCompleto(),
            'curso' => $pago->inscripcion?->oferta?->curso?->nombre,
            'monto' => $pago->monto,
            'estadoPago' => $pago->estado,
            'estadoTransaccion' => $transaccion?->estado,
            'codigoTransaccion' => $transaccion?->codigo_transaccion,
            'codigoQr' => $transaccion?->codigo_qr,
            'urlQr' => $transaccion?->url_qr,
            'checkoutUrl' => $transaccion?->checkout_url,
            'deepLink' => $transaccion?->deep_link,
            'qrContentUrl' => $transaccion?->qr_content_url,
            'universalUrl' => $transaccion?->universal_url,
            'concepto' => $transaccion?->concepto,
            'fechaGeneracion' => optional($transaccion?->fecha_generacion)->format('d/m/Y H:i'),
            'fechaVencimiento' => optional($transaccion?->fecha_vencimiento)->format('d/m/Y H:i'),
            'fechaConfirmacion' => optional($transaccion?->fecha_confirmacion)->format('d/m/Y H:i'),
            'saldoPendiente' => $pago->inscripcion?->saldo_pendiente,
        ];
    }

    private function verificarAccesoEstudiante(Pago $pago): void
    {
        $usuario = request()->user();

        if ($usuario?->tieneRol('estudiante') && $pago->inscripcion?->estudiante?->user_id !== $usuario->id) {
            abort(403, 'Solo puede ver pagos QR de sus propias inscripciones.');
        }
    }
}
