<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $fillable=[
        'id_servicio',
        'fecha_reserva',
        'id_cliente'
        ];
    public $timestamps=false;
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }

}
