<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Publicación de un material reciclable.
 *
 * user_id y estado NO son asignables en masa (T35): el dueño se fija con
 * $request->user()->materiales()->create([...]) y el estado lo cambian las
 * reglas del sistema, nunca un formulario.
 */
class Material extends Model
{
    use HasFactory;

    public const DISPONIBLE = 'disponible';

    public const RESERVADO = 'reservado';

    public const RETIRADO = 'retirado';

    public const CANCELADO = 'cancelado';

    protected $table = 'materiales';

    protected $fillable = [
        'categoria_id', 'barrio_id', 'descripcion', 'cantidad', 'unidad',
        'direccion_referencia', 'latitud', 'longitud', 'foto_path',
    ];

    protected $attributes = [
        'estado' => self::DISPONIBLE,
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:2',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    public function vecino(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaResiduo::class, 'categoria_id');
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }

    public function solicitudes(): BelongsToMany
    {
        return $this->belongsToMany(SolicitudRetiro::class, 'solicitud_material', 'material_id', 'solicitud_id')
            ->withPivot('peso_real_kg');
    }
}
