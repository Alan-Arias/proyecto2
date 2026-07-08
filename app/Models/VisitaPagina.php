<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitaPagina extends Model
{
    protected $table = 'page_visits';

    protected $fillable = [
        'pagina',
        'visitas',
    ];
}
