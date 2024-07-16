<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable=[
        'nombres',
        'edad',
        'sexo',
        'usuario_id'        
        ];
    public $timestamps=false;
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_cliente');
    }
    public function pago()
    {
        return $this->hasMany(pago::class, 'id_cliente');
    }
}
