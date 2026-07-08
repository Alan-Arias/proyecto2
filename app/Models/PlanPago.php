<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanPago extends Model
{
    protected $table = 'payment_plans';

    protected $fillable = [
        'enrollment_id',
        'monto_total',
        'numero_cuotas',
        'estado',
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'enrollment_id');
    }

    public function cuotas(): HasMany
    {
        return $this->hasMany(CuotaPago::class, 'payment_plan_id');
    }
}
