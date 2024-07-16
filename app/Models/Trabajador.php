<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    protected $table = 'trabajadors';
    protected $fillable=[
        'nombres',
        'edad',
        'sexo',
        'usuario_id'     
        ];
    public $timestamps=false;
    public function usuarios()
    {
        return $this->hasMany(Usuarios::class);
    }
}
