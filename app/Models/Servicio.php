<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable=[
        'nombre',
        'descripcion',
        'costo',
        'duracion_estimada',
        'estado'
        ];
    public $timestamps=false;
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_servicio');
    }
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'id_trabajador');
    }
    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'id_promocion');
    }
    public function asignacionservicio()
    {
        return $this->hasMany(AsignacionServicio::class, 'id_servicio');
    }
}
