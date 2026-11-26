<?php

namespace App\Policies;

use App\Models\Material;
use App\Models\User;

/**
 * Quién puede hacer qué con una publicación de material (T16 / HU6 / HU7 / RF07).
 *
 * Laravel la asocia sola al modelo App\Models\Material (por la convención de
 * nombres). Uso desde el controlador de materiales:
 *
 *   $this->authorize('create', Material::class);
 *   $this->authorize('update', $material);   // editar
 *   $this->authorize('delete', $material);   // retirar la publicación
 *
 * En las vistas:  @can('update', $material) ... @endcan
 */
class MaterialPolicy
{
    /** Estado en el que la publicación todavía se puede modificar. */
    public const ESTADO_EDITABLE = 'disponible';

    /**
     * Publicar materiales: los roles con el permiso materiales.publicar (vecinos).
     */
    public function create(User $user): bool
    {
        return $user->tienePermiso('materiales.publicar');
    }

    /**
     * Editar: solo el dueño y solo mientras está disponible.
     * Si ya está reservado en una solicitud de retiro, no se puede cambiar.
     */
    public function update(User $user, Material $material): bool
    {
        return $this->esDelUsuario($user, $material)
            && $material->estado === self::ESTADO_EDITABLE;
    }

    /**
     * Retirar la publicación: mismas condiciones que editar.
     */
    public function delete(User $user, Material $material): bool
    {
        return $this->update($user, $material);
    }

    private function esDelUsuario(User $user, Material $material): bool
    {
        return (int) $material->user_id === (int) $user->id;
    }
}
