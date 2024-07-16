<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    protected $table = 'usuarios';
    protected $fillable=[
    'user',
    'password',
    'tipo_user',
    'desc_user'    
    ];
    public $timestamps=false;
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
}
