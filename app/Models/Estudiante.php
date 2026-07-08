<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'nombre',
        'apellido',
        'cedula',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'student_id');
    }

    public function nombreCompleto(): string
    {
        return trim($this->nombre.' '.$this->apellido);
    }
}
