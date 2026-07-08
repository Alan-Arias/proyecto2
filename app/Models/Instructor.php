<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instructor extends Model
{
    protected $table = 'instructors';

    protected $fillable = [
        'user_id',
        'nombre',
        'apellido',
        'cedula',
        'telefono',
        'especialidad',
        'estado',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class, 'instructor_id');
    }

    public function nombreCompleto(): string
    {
        return trim($this->nombre.' '.$this->apellido);
    }
}
