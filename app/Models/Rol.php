<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
     public const ADMIN = 1;
     public const VECINO = 2;
     public const RECOLECTOR = 3;

    protected $table = 'roles';
    protected $fillable = [
        'nombre',
        'descripcion', ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'rol_id');
    }
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'rol_id', 'permiso_id');
    }
    
}
