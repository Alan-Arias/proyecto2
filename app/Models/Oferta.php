<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Oferta extends Model
{
    protected $table = 'offers';

    protected $fillable = [
        'course_id',
        'instructor_id',
        'codigo',
        'fecha_inicio',
        'fecha_fin',
        'cupos',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'course_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'offer_id');
    }

    public function cuposDisponibles(): int
    {
        return max(0, $this->cupos - $this->inscripciones()->count());
    }
}
