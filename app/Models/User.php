<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// rol_id y activo NO son asignables en masa (T35): solo los cambia el sistema
// o un administrador, asignándolos uno por uno.
#[Fillable(['name', 'email', 'password', 'apellido', 'telefono', 'barrio_id', 'direccion'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
    protected $attributes = [
        'rol_id' => Rol::VECINO,
        'activo' => true,
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }

    /** Materiales que publicó el vecino. */
    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class, 'user_id');
    }

    /** Solicitudes de retiro que pidió el vecino. */
    public function solicitudes(): HasMany
    {
        return $this->hasMany(SolicitudRetiro::class, 'vecino_id');
    }

    /** Retiros que el administrador le asignó al recolector (T33). */
    public function retirosAsignados(): HasMany
    {
        return $this->hasMany(SolicitudRetiro::class, 'recolector_id');
    }

    public function esRecolector(): bool
    {
        return (int) $this->rol_id === Rol::RECOLECTOR;
    }

    public function esAdmin(): bool
    {
        return (int) $this->rol_id === Rol::ADMIN;
    }

    public function tienePermiso(string $clave): bool
    {
        return $this->rol->permisos->contains('clave', $clave);
    }
}
