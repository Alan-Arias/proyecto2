<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calificacion extends Model
{
    protected $table = 'grades';

    protected $fillable = [
        'enrollment_id',
        'nota',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'nota' => 'decimal:2',
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'enrollment_id');
    }

    public function definirEstado(): string
    {
        return (float) $this->nota >= 51 ? 'aprobado' : 'reprobado';
    }
}
