<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inscripcion extends Model
{
    protected $table = 'enrollments';

    protected $fillable = [
        'student_id',
        'offer_id',
        'fecha_inscripcion',
        'estado_academico',
        'estado_pago',
        'precio_total',
        'saldo_pendiente',
    ];

    protected $casts = [
        'fecha_inscripcion' => 'date',
        'precio_total' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'student_id');
    }

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class, 'offer_id');
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'enrollment_id');
    }

    public function calificacion(): HasOne
    {
        return $this->hasOne(Calificacion::class, 'enrollment_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'enrollment_id');
    }

    public function transaccionesPagoFacil(): HasMany
    {
        return $this->hasMany(TransaccionPagoFacil::class, 'inscripcion_id');
    }

    public function planPago(): HasOne
    {
        return $this->hasOne(PlanPago::class, 'enrollment_id');
    }

    public function certificado(): HasOne
    {
        return $this->hasOne(Certificado::class, 'enrollment_id');
    }

    public function calcularSaldoPendiente(): float
    {
        $montoPagado = (float) $this->pagos()
            ->whereIn('estado', ['registrado', 'confirmado'])
            ->sum('monto');

        return max(0, (float) $this->precio_total - $montoPagado);
    }

    public function actualizarEstadoPago(): void
    {
        $saldoPendiente = $this->calcularSaldoPendiente();
        $estadoPago = 'pendiente';

        if ($saldoPendiente <= 0) {
            $estadoPago = 'pagado';
        } elseif ($saldoPendiente < (float) $this->precio_total) {
            $estadoPago = 'parcial';
        }

        $this->update([
            'saldo_pendiente' => $saldoPendiente,
            'estado_pago' => $estadoPago,
        ]);
    }

    public function estaAprobada(): bool
    {
        return (float) ($this->calificacion?->nota ?? 0) >= 51;
    }
}
