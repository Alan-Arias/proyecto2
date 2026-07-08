<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuotaPago extends Model
{
    protected $table = 'payment_installments';

    protected $fillable = [
        'payment_plan_id',
        'numero_cuota',
        'monto',
        'fecha_vencimiento',
        'fecha_pago',
        'estado',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
    ];

    public function planPago(): BelongsTo
    {
        return $this->belongsTo(PlanPago::class, 'payment_plan_id');
    }
}
