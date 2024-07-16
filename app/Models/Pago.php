<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable=[
        'razon_social',
        'correo',
        'monto',
        'servicio' ,
        'id_cliente'
        ];
    public $timestamps=false;
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
}
