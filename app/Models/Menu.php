<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'parent_id',
        'nombre',
        'ruta',
        'icono',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'menu_role', 'menu_id', 'role_id')
            ->withTimestamps();
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('orden');
    }
}
