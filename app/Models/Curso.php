<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    protected $table = 'courses';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'duracion_horas',
        'tipo_licencia',
        'tema_visual',
        'estado',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function materias(): BelongsToMany
    {
        return $this->belongsToMany(Materia::class, 'course_subject', 'course_id', 'subject_id')
            ->withTimestamps();
    }

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class, 'course_id');
    }
}
