<?php

namespace App\Services;

use App\Models\Inscripcion;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\TransaccionPagoFacil;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PagoFacilService
{
    public function prepararSolicitudQr(Inscripcion $inscripcion, float $monto): array
    {
        $inscripcion->loadMissing(['estudiante.usuario', 'oferta.curso']);

        return [
            'inscripcion_id' => $inscripcion->id,
            'estudiante_id' => $inscripcion->student_id,
            'monto' => round($monto, 2),
            'moneda' => 'BOB',
            'concepto' => 'Pago curso '.$inscripcion->oferta?->curso?->nombre,
            'modo' => config('services.pagofacil.modo', 'simulado'),
            'url_base' => $this->urlBase(),
        ];
    }

    public function generarQrPago(Inscripcion $inscripcion, float $monto, ?string $referencia = null): Pago
    {
        if (! $this->usaApiReal()) {
            return $this->generarQrSimulado($inscripcion, $monto, $referencia);
        }

        $solicitud = $this->prepararSolicitudQr($inscripcion, $monto);
        $codigoTransaccion = $this->generarCodigoTransaccion($referencia);
        $paymentMethodId = $this->resolverPaymentMethodId();
        $cuerpo = $this->cuerpoGenerarQr($inscripcion, $solicitud, $codigoTransaccion, $paymentMethodId);
        $respuesta = $this->clienteAutenticado()
            ->post($this->endpoint('/generate-qr'), $cuerpo);
        $datosRespuesta = $this->validarRespuesta($respuesta, 'generar QR');
        $valores = $datosRespuesta['values'] ?? [];

        $metodoQr = $this->metodoPagoQr();
        $pago = Pago::create([
            'enrollment_id' => $inscripcion->id,
            'payment_method_id' => $metodoQr->id,
            'monto' => $solicitud['monto'],
            'fecha_pago' => now()->toDateString(),
            'referencia' => $referencia ?: $codigoTransaccion,
            'comprobante' => 'QR PagoFacil pendiente',
            'estado' => 'pendiente',
            'observacion' => 'Transaccion QR generada con PagoFacil.',
        ]);

        $this->registrarTransaccionPagoFacil($pago, [
            'codigo_transaccion' => $codigoTransaccion,
            'pagofacil_transaction_id' => isset($valores['transactionId']) ? (string) $valores['transactionId'] : null,
            'payment_method_id_pago_facil' => $paymentMethodId,
            'codigo_qr' => $this->normalizarImagenQr($valores['qrBase64'] ?? null),
            'url_qr' => $valores['qrContentUrl'] ?? null,
            'checkout_url' => $valores['checkoutUrl'] ?? null,
            'deep_link' => $valores['deepLink'] ?? null,
            'qr_content_url' => $valores['qrContentUrl'] ?? null,
            'universal_url' => $valores['universalUrl'] ?? null,
            'monto' => $solicitud['monto'],
            'moneda' => $solicitud['moneda'],
            'concepto' => $solicitud['concepto'],
            'estado' => $this->normalizarEstado($valores['status'] ?? 1),
            'fecha_generacion' => now(),
            'fecha_vencimiento' => $this->parsearFecha($valores['expirationDate'] ?? null) ?? now()->addHours(24),
            'respuesta_pago_facil' => $datosRespuesta,
        ]);

        $inscripcion->actualizarEstadoPago();

        return $pago->load('transaccionPagoFacil');
    }

    public function consultarEstadoPago(Pago $pago): TransaccionPagoFacil
    {
        $pago->loadMissing('transaccionPagoFacil');
        $transaccion = $pago->transaccionPagoFacil;

        if (! $transaccion) {
            throw new RuntimeException('El pago no tiene una transaccion QR asociada.');
        }

        if (! $this->usaApiReal()) {
            return $this->consultarEstadoSimulado($pago);
        }

        $cuerpo = array_filter([
            'pagofacilTransactionId' => $this->numeroSiAplica($transaccion->pagofacil_transaction_id),
            'companyTransactionId' => $transaccion->codigo_transaccion,
        ], fn ($valor) => $valor !== null && $valor !== '');

        $respuesta = $this->clienteAutenticado()
            ->post($this->endpoint('/query-transaction'), $cuerpo);
        $datosRespuesta = $this->validarRespuesta($respuesta, 'consultar transaccion');
        $valores = $datosRespuesta['values'] ?? [];

        return $this->actualizarEstadoTransaccion(
            $transaccion,
            $valores['paymentStatus'] ?? null,
            ['consulta' => $datosRespuesta],
            $this->parsearFecha($valores['paymentDate'] ?? null),
        );
    }

    public function procesarCallback(array $payload): ?TransaccionPagoFacil
    {
        $pedidoId = (string) ($payload['PedidoID'] ?? $payload['paymentNumber'] ?? $payload['companyTransactionId'] ?? '');

        if ($pedidoId === '') {
            return null;
        }

        $transaccion = TransaccionPagoFacil::query()
            ->where('codigo_transaccion', $pedidoId)
            ->orWhere('pagofacil_transaction_id', $pedidoId)
            ->first();

        if (! $transaccion) {
            return null;
        }

        $fechaPago = $this->parsearFecha(trim(($payload['Fecha'] ?? '').' '.($payload['Hora'] ?? '')));

        return $this->actualizarEstadoTransaccion(
            $transaccion,
            $payload['Estado'] ?? null,
            ['callback' => $payload, 'callback_recibido_en' => now()->toDateTimeString()],
            $fechaPago,
        );
    }

    public function verificarRespuestaPago(array $respuesta): bool
    {
        return $this->normalizarEstado($respuesta['estado'] ?? $respuesta['Estado'] ?? null) === 'confirmado';
    }

    public function registrarTransaccionPagoFacil(Pago $pago, array $datos): TransaccionPagoFacil
    {
        $pago->loadMissing('inscripcion');

        return TransaccionPagoFacil::create($datos + [
            'pago_id' => $pago->id,
            'inscripcion_id' => $pago->enrollment_id,
            'estudiante_id' => $pago->inscripcion?->student_id,
        ]);
    }

    public function confirmarTransaccionAcademica(Pago $pago): TransaccionPagoFacil
    {
        if ($this->usaApiReal()) {
            return $this->consultarEstadoPago($pago);
        }

        $transaccion = $pago->transaccionPagoFacil;
        $respuesta = [
            'estado' => 'confirmado',
            'modo' => config('services.pagofacil.modo', 'simulado'),
            'mensaje' => 'Pago QR confirmado manualmente para la defensa universitaria.',
            'confirmado_en' => now()->toDateTimeString(),
        ];

        if ($transaccion && $this->verificarRespuestaPago($respuesta)) {
            return $this->actualizarEstadoTransaccion($transaccion, 'confirmado', $respuesta, now());
        }

        return $pago->refresh()->transaccionPagoFacil;
    }

    public function cancelarTransaccionAcademica(Pago $pago): ?TransaccionPagoFacil
    {
        $transaccion = $pago->transaccionPagoFacil;

        if ($transaccion && $transaccion->estado !== 'confirmado') {
            $transaccion->update([
                'estado' => 'cancelado',
                'respuesta_pago_facil' => array_merge($transaccion->respuesta_pago_facil ?? [], [
                    'modo' => config('services.pagofacil.modo', 'simulado'),
                    'mensaje' => 'Pago QR cancelado desde el panel academico.',
                    'cancelado_en' => now()->toDateTimeString(),
                ]),
            ]);
            $pago->update(['estado' => 'cancelado']);
            $pago->inscripcion?->actualizarEstadoPago();
        }

        return $pago->refresh()->transaccionPagoFacil;
    }

    public function listarServiciosHabilitados(): array
    {
        if (! $this->usaApiReal()) {
            return [];
        }

        $respuesta = $this->clienteAutenticado()
            ->post($this->endpoint('/list-enabled-services'));
        $datosRespuesta = $this->validarRespuesta($respuesta, 'listar metodos QR');

        return $datosRespuesta['values'] ?? [];
    }

    private function generarQrSimulado(Inscripcion $inscripcion, float $monto, ?string $referencia = null): Pago
    {
        $solicitud = $this->prepararSolicitudQr($inscripcion, $monto);
        $codigoTransaccion = $this->generarCodigoTransaccion($referencia);
        $codigoQr = $this->crearCodigoQrAcademico($codigoTransaccion);
        $metodoQr = $this->metodoPagoQr();

        $pago = Pago::create([
            'enrollment_id' => $inscripcion->id,
            'payment_method_id' => $metodoQr->id,
            'monto' => $solicitud['monto'],
            'fecha_pago' => now()->toDateString(),
            'referencia' => $referencia ?: $codigoTransaccion,
            'comprobante' => 'QR academico PagoFacil pendiente',
            'estado' => 'pendiente',
            'observacion' => 'Transaccion QR generada en modo simulado.',
        ]);

        $this->registrarTransaccionPagoFacil($pago, [
            'codigo_transaccion' => $codigoTransaccion,
            'codigo_qr' => $codigoQr,
            'url_qr' => null,
            'monto' => $solicitud['monto'],
            'moneda' => $solicitud['moneda'],
            'concepto' => $solicitud['concepto'],
            'estado' => 'pendiente',
            'fecha_generacion' => now(),
            'fecha_vencimiento' => now()->addHours(24),
            'respuesta_pago_facil' => [
                'modo' => $solicitud['modo'],
                'mensaje' => 'Simulacion academica. En produccion se usa la API MasterQR de PagoFacil.',
            ],
        ]);

        $inscripcion->actualizarEstadoPago();

        return $pago->load('transaccionPagoFacil');
    }

    private function consultarEstadoSimulado(Pago $pago): TransaccionPagoFacil
    {
        $transaccion = $pago->transaccionPagoFacil;

        if ($transaccion && $transaccion->estaPendiente() && $transaccion->fecha_vencimiento?->isPast()) {
            $transaccion->update([
                'estado' => 'vencido',
                'respuesta_pago_facil' => array_merge($transaccion->respuesta_pago_facil ?? [], [
                    'mensaje' => 'El pago QR academico vencio antes de confirmarse.',
                    'consultado_en' => now()->toDateTimeString(),
                ]),
            ]);
            $pago->update(['estado' => 'vencido']);
        }

        return $pago->refresh()->transaccionPagoFacil;
    }

    private function cuerpoGenerarQr(Inscripcion $inscripcion, array $solicitud, string $codigoTransaccion, int $paymentMethodId): array
    {
        $estudiante = $inscripcion->estudiante;
        $curso = $inscripcion->oferta?->curso;
        $concepto = $this->textoPagoFacil($solicitud['concepto'], 'Pago curso');
        $monto = round((float) $solicitud['monto'], 2);

        return [
            'paymentMethod' => $paymentMethodId,
            'clientName' => $this->textoPagoFacil($estudiante?->nombreCompleto(), 'Cliente Autoescuela'),
            'documentType' => 1,
            'documentId' => $this->documentoPagoFacil($estudiante?->cedula, $inscripcion->student_id),
            'phoneNumber' => $this->telefonoPagoFacil($estudiante?->telefono),
            'email' => $this->emailPagoFacil($estudiante?->usuario?->email),
            'paymentNumber' => $codigoTransaccion,
            'amount' => $monto,
            'currency' => (int) config('services.pagofacil.currency', 2),
            'clientCode' => (string) $inscripcion->student_id,
            'callbackUrl' => $this->callbackUrl(),
            'orderDetail' => [[
                'serial' => 1,
                'product' => $this->textoPagoFacil($curso?->nombre, $concepto),
                'quantity' => 1,
                'price' => $monto,
                'discount' => 0,
                'total' => $monto,
            ]],
        ];
    }

    private function metodoPagoQr(): MetodoPago
    {
        return MetodoPago::firstOrCreate(
            ['nombre' => 'QR'],
            ['descripcion' => 'Pago por QR con PagoFacil.', 'activo' => true],
        );
    }

    private function clienteAutenticado()
    {
        return $this->cliente()->withToken($this->obtenerAccessToken());
    }

    private function cliente()
    {
        return Http::timeout((int) config('services.pagofacil.timeout', 20))
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Response-Language' => config('services.pagofacil.response_language', 'es'),
            ]);
    }

    private function obtenerAccessToken(): string
    {
        $this->validarCredenciales();

        $cacheKey = 'pagofacil.access_token.'.hash('sha256', (string) config('services.pagofacil.token_service'));
        $tokenCacheado = Cache::get($cacheKey);

        if ($tokenCacheado) {
            return $tokenCacheado;
        }

        $respuesta = $this->cliente()
            ->withHeaders([
                'tcTokenService' => config('services.pagofacil.token_service'),
                'tcTokenSecret' => config('services.pagofacil.token_secret'),
            ])
            ->post($this->endpoint('/login'));
        $datosRespuesta = $this->validarRespuesta($respuesta, 'autenticacion');
        $valores = $datosRespuesta['values'] ?? [];
        $token = $valores['accessToken'] ?? null;

        if (! $token) {
            throw new RuntimeException('PagoFacil no devolvio accessToken.');
        }

        $minutos = max(1, (int) floor(((float) ($valores['expiresInMinutes'] ?? 30)) - 1));
        Cache::put($cacheKey, $token, now()->addMinutes($minutos));

        return $token;
    }

    private function validarCredenciales(): void
    {
        $faltantes = collect([
            'PAGOFACIL_URL_BASE' => $this->urlBase(),
            'PAGOFACIL_TOKEN_SERVICE' => config('services.pagofacil.token_service'),
            'PAGOFACIL_TOKEN_SECRET' => config('services.pagofacil.token_secret'),
        ])->filter(fn ($valor) => blank($valor))->keys()->all();

        if ($faltantes) {
            throw new RuntimeException('Faltan credenciales PagoFacil: '.implode(', ', $faltantes).'.');
        }
    }

    private function validarRespuesta($respuesta, string $accion): array
    {
        $datos = $respuesta->json();

        if (! $respuesta->successful()) {
            $mensaje = is_array($datos) ? ($datos['message'] ?? null) : null;
            $detalle = $mensaje ? ': '.$mensaje : '.';

            throw new RuntimeException('PagoFacil rechazo la accion '.$accion.' con HTTP '.$respuesta->status().$detalle);
        }

        if (! is_array($datos)) {
            throw new RuntimeException('PagoFacil devolvio una respuesta invalida al '.$accion.'.');
        }

        if ((int) ($datos['error'] ?? 1) !== 0) {
            throw new RuntimeException((string) ($datos['message'] ?? 'PagoFacil reporto un error al '.$accion.'.'));
        }

        return $datos;
    }

    private function resolverPaymentMethodId(): int
    {
        $configurado = config('services.pagofacil.payment_method_id');

        if ($configurado) {
            return (int) $configurado;
        }

        $servicios = collect($this->listarServiciosHabilitados());
        $servicio = $servicios->first(fn (array $item) => ($item['currencyName'] ?? '') === 'BOB')
            ?: $servicios->first();

        if (! $servicio || empty($servicio['paymentMethodId'])) {
            throw new RuntimeException('No se pudo obtener paymentMethodId habilitado para PagoFacil.');
        }

        return (int) $servicio['paymentMethodId'];
    }

    private function actualizarEstadoTransaccion(TransaccionPagoFacil $transaccion, mixed $estadoPagoFacil, array $respuesta, ?Carbon $fechaPago = null): TransaccionPagoFacil
    {
        $estado = $this->normalizarEstado($estadoPagoFacil);
        $datosRespuesta = array_merge($transaccion->respuesta_pago_facil ?? [], $respuesta);
        $datosTransaccion = [
            'estado' => $estado,
            'respuesta_pago_facil' => $datosRespuesta,
        ];

        if ($estado === 'confirmado') {
            $datosTransaccion['fecha_confirmacion'] = $fechaPago ?? now();
        }

        $transaccion->update($datosTransaccion);
        $pago = $transaccion->pago;

        if ($pago) {
            $pago->update([
                'estado' => match ($estado) {
                    'confirmado' => 'confirmado',
                    'cancelado', 'revertido' => 'cancelado',
                    'vencido' => 'vencido',
                    default => 'pendiente',
                },
                'fecha_pago' => $estado === 'confirmado' ? ($fechaPago ?? now())->toDateString() : $pago->fecha_pago,
                'comprobante' => $estado === 'confirmado'
                    ? 'Comprobante QR PagoFacil '.$transaccion->codigo_transaccion
                    : $pago->comprobante,
            ]);
            $pago->inscripcion?->actualizarEstadoPago();
        }

        return $transaccion->refresh();
    }

    private function normalizarEstado(mixed $estado): string
    {
        $valor = Str::of((string) $estado)->lower()->ascii()->trim()->toString();

        return match ($valor) {
            '2', 'pagado', 'pago exitoso', 'confirmado', 'completado', 'paid', 'success' => 'confirmado',
            '3', 'revertido', 'reversed' => 'revertido',
            '4', 'anulado', 'cancelado', 'cancelled', 'canceled' => 'cancelado',
            '5', 'vencido', 'expirado', 'expired' => 'vencido',
            default => 'pendiente',
        };
    }

    private function normalizarImagenQr(?string $qrBase64): ?string
    {
        if (! $qrBase64) {
            return null;
        }

        if (str_starts_with($qrBase64, 'data:image')) {
            return $qrBase64;
        }

        return 'data:image/png;base64,'.$qrBase64;
    }

    private function parsearFecha(?string $fecha): ?Carbon
    {
        if (! $fecha || blank(trim($fecha))) {
            return null;
        }

        try {
            return Carbon::parse($fecha);
        } catch (Throwable) {
            return null;
        }
    }

    private function numeroSiAplica(?string $valor): int|string|null
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        return ctype_digit($valor) ? (int) $valor : $valor;
    }

    private function generarCodigoTransaccion(?string $referencia = null): string
    {
        if ($referencia) {
            return $referencia;
        }

        return 'PF-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));
    }

    private function textoPagoFacil(?string $valor, string $respaldo): string
    {
        $texto = Str::of($valor ?: $respaldo)
            ->ascii()
            ->replaceMatches('/[^A-Za-z0-9 .,_-]+/', ' ')
            ->squish()
            ->limit(120, '')
            ->toString();

        return $texto !== '' ? $texto : $respaldo;
    }

    private function documentoPagoFacil(?string $valor, int $respaldo): string
    {
        $documento = preg_replace('/\D+/', '', (string) $valor);

        return $documento !== '' ? $documento : (string) $respaldo;
    }

    private function telefonoPagoFacil(?string $valor): string
    {
        $telefono = preg_replace('/\D+/', '', (string) $valor);

        if (strlen($telefono) === 8) {
            return $telefono;
        }

        return (string) config('services.pagofacil.default_phone', '77777777');
    }

    private function emailPagoFacil(?string $valor): string
    {
        $email = trim((string) $valor);
        $dominio = Str::of(Str::after($email, '@'))->lower()->toString();

        if (filter_var($email, FILTER_VALIDATE_EMAIL)
            && ! Str::endsWith($dominio, ['.test', '.local', '.invalid'])) {
            return $email;
        }

        return (string) config('services.pagofacil.default_email', 'pagos@autoescuelaamerica.com.bo');
    }

    private function callbackUrl(): string
    {
        $url = config('services.pagofacil.callback_url') ?: route('pagofacil.callback', [], true);
        $partes = parse_url($url);
        $host = Str::of($partes['host'] ?? '')->lower()->toString();
        $esHttps = ($partes['scheme'] ?? null) === 'https';
        $hostLocal = $host === ''
            || in_array($host, ['localhost', '127.0.0.1', '::1'], true)
            || Str::endsWith($host, ['.test', '.local', '.invalid'])
            || preg_match('/^(10\.|192\.168\.|172\.(1[6-9]|2\d|3[0-1])\.)/', $host) === 1;

        if (! $esHttps || $hostLocal) {
            throw new RuntimeException(
                'PagoFacil requiere PAGOFACIL_CALLBACK_URL con una URL publica HTTPS. '
                .'Configura un dominio publicado o un tunel HTTPS antes de generar QR reales.'
            );
        }

        return $url;
    }

    private function endpoint(string $path): string
    {
        return rtrim($this->urlBase(), '/').'/'.ltrim($path, '/');
    }

    private function urlBase(): string
    {
        return rtrim((string) config('services.pagofacil.url_base', 'https://masterqr.pagofacil.com.bo/api/services/v2'), '/');
    }

    private function usaApiReal(): bool
    {
        $modo = Str::of((string) config('services.pagofacil.modo', 'simulado'))->lower()->ascii()->trim()->toString();

        return ! app()->environment('testing') && ! in_array($modo, ['simulado', 'simulacion', 'fake', 'test'], true);
    }

    private function crearCodigoQrAcademico(string $codigoTransaccion): string
    {
        $hash = hash('sha256', $codigoTransaccion);
        $celdas = '';
        $tamanoCelda = 14;

        for ($fila = 0; $fila < 15; $fila++) {
            for ($columna = 0; $columna < 15; $columna++) {
                $indice = ($fila * 15 + $columna) % strlen($hash);
                $debePintar = hexdec($hash[$indice]) % 2 === 0;

                if ($fila < 3 && $columna < 3 || $fila < 3 && $columna > 11 || $fila > 11 && $columna < 3) {
                    $debePintar = true;
                }

                if ($debePintar) {
                    $x = 10 + ($columna * $tamanoCelda);
                    $y = 10 + ($fila * $tamanoCelda);
                    $celdas .= "<rect x=\"{$x}\" y=\"{$y}\" width=\"{$tamanoCelda}\" height=\"{$tamanoCelda}\" rx=\"2\" fill=\"#0f172a\"/>";
                }
            }
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="230" height="260" viewBox="0 0 230 260">'
            .'<rect width="230" height="260" rx="12" fill="#ffffff"/>'
            .'<rect x="6" y="6" width="218" height="218" rx="10" fill="#ffffff" stroke="#0f172a" stroke-width="2"/>'
            .$celdas
            .'<text x="115" y="247" text-anchor="middle" font-family="Arial" font-size="13" fill="#0f172a">PagoFacil simulado</text>'
            .'</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
