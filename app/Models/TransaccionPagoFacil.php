<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaccionPagoFacil extends Model
{
    protected $table = 'transacciones_pago_facil';

    protected $fillable = [
        'pago_id',
        'inscripcion_id',
        'estudiante_id',
        'codigo_transaccion',
        'pagofacil_transaction_id',
        'payment_method_id_pago_facil',
        'codigo_qr',
        'url_qr',
        'checkout_url',
        'deep_link',
        'qr_content_url',
        'universal_url',
        'monto',
        'moneda',
        'concepto',
        'estado',
        'fecha_generacion',
        'fecha_vencimiento',
        'fecha_confirmacion',
        'respuesta_pago_facil',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_generacion' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'fecha_confirmacion' => 'datetime',
        'respuesta_pago_facil' => 'array',
    ];

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'inscripcion_id');
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function estaPendiente(): bool
    {
        return in_array($this->estado, ['generado', 'pendiente'], true);
    }
}
