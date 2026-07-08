<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Materia extends Model
{
    protected $table = 'subjects';

    protected $fillable = [
        'nombre',
        'descripcion',
        'horas',
    ];

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'course_subject', 'subject_id', 'course_id')
            ->withTimestamps();
    }
}
