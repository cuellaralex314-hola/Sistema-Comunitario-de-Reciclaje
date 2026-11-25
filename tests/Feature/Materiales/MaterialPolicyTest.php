<?php

namespace Tests\Feature\Materiales;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Policy de materiales (T16).
 *
 * Estos tests se saltean hasta que exista el modelo App\Models\Material
 * (T07/T08 de Fabián). Cuando exista, se ejecutan solos.
 */
class MaterialPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        if (! class_exists(\App\Models\Material::class)) {
            $this->markTestSkipped('Falta el modelo Material (T07/T08).');
        }
    }

    private function usuario(int $id, int $rol = Rol::VECINO): User
    {
        $user = new User;
        $user->forceFill(['id' => $id, 'rol_id' => $rol]);

        return $user;
    }

    private function material(int $duenio, string $estado)
    {
        $material = new \App\Models\Material;
        $material->forceFill(['user_id' => $duenio, 'estado' => $estado]);

        return $material;
    }

    public function test_el_duenio_edita_su_material_disponible(): void
    {
        $this->assertTrue(Gate::forUser($this->usuario(1))->allows('update', $this->material(1, 'disponible')));
        $this->assertTrue(Gate::forUser($this->usuario(1))->allows('delete', $this->material(1, 'disponible')));
    }

    public function test_nadie_mas_edita_un_material_ajeno(): void
    {
        $this->assertFalse(Gate::forUser($this->usuario(2))->allows('update', $this->material(1, 'disponible')));
        $this->assertFalse(Gate::forUser($this->usuario(2, Rol::ADMIN))->allows('update', $this->material(1, 'disponible')));
    }

    public function test_no_se_edita_un_material_reservado_o_retirado(): void
    {
        foreach (['reservado', 'retirado', 'cancelado'] as $estado) {
            $this->assertFalse(Gate::forUser($this->usuario(1))->allows('update', $this->material(1, $estado)), $estado);
        }
    }
}
