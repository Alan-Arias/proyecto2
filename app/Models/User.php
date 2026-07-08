<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'telefono',
        'password',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'role_user', 'user_id', 'role_id')
            ->withTimestamps();
    }

    public function instructor(): HasOne
    {
        return $this->hasOne(Instructor::class, 'user_id');
    }

    public function estudiante(): HasOne
    {
        return $this->hasOne(Estudiante::class, 'user_id');
    }

    public function tieneRol(string|array $rolesPermitidos): bool
    {
        $rolesPermitidos = (array) $rolesPermitidos;

        return $this->roles()
            ->whereIn('nombre', $rolesPermitidos)
            ->exists();
    }
}
