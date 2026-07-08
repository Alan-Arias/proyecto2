<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pago extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'enrollment_id',
        'payment_method_id',
        'monto',
        'fecha_pago',
        'referencia',
        'comprobante',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'enrollment_id');
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'payment_method_id');
    }

    public function transaccionPagoFacil(): HasOne
    {
        return $this->hasOne(TransaccionPagoFacil::class, 'pago_id');
    }
}
