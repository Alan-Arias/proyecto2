<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'enrollment_id',
        'fecha',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'enrollment_id');
    }
}
