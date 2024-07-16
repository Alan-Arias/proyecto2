<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $fillable=[
        'id_cliente',
        'marca',
        'modelo',
        'año',
        'color',
        'placa'
        ];
    public $timestamps=false;
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
    public function asignacionServicios()
    {
        return $this->belongsToMany(AsignacionServicio::class, 'detalle_vehiculo')
                    ->withPivot('fecha_servicio');
    }
    public function detalle_vehiculos()
    {
        return $this->hasMany(DetalleVehiculo::class);
    }
    
}
