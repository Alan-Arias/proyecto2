<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $fillable=[
        'descripcion',
        'descuento',
        'estado',
        'fecha_inicio',
        'fecha_fin'
        ];
    public $timestamps=false;
    public function servicio()
    {
        return $this->hasMany(Servicio::class, 'id_promocion');
    }

}
