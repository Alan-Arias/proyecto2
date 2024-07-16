<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionServicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_asignacion',
        'estado',
        'id_servicio',
        'id_trabajador'
    ];
    public $timestamps=false;
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'id_trabajador');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }
    public function vehiculos()
    {
        return $this->belongsToMany(Vehiculo::class, 'detalle_vehiculo')
                    ->withPivot('fecha_servicio');
    }
}
