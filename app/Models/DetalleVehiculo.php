<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVehiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehiculo_id',
        'asignacion_servicio_id',
        'fecha_servicio',
    ];

    public $timestamps = false;

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function asignacion_servicio()
    {
        return $this->belongsTo(AsignacionServicio::class);
    }
}
