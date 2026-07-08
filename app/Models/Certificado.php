<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificado extends Model
{
    protected $table = 'certificates';

    protected $fillable = [
        'enrollment_id',
        'codigo',
        'fecha_emision',
        'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'enrollment_id');
    }
}
