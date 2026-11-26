<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\Rol;
use App\Models\SolicitudRetiro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Revisión de seguridad (T35): los campos sensibles no se pueden
 * cambiar con asignación masiva, aunque alguien los mande en un formulario.
 */
class SeguridadTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_el_rol_y_el_estado_de_un_usuario_no_se_asignan_en_masa(): void
    {
        $vecino = User::factory()->create();

        $vecino->update(['rol_id' => Rol::ADMIN, 'activo' => false, 'name' => 'Laura']);

        $vecino->refresh();
        $this->assertSame(Rol::VECINO, (int) $vecino->rol_id);
        $this->assertTrue($vecino->activo);
        $this->assertSame('Laura', $vecino->name);
    }

    public function test_el_estado_y_el_duenio_de_un_material_no_se_asignan_en_masa(): void
    {
        $material = Material::factory()->create();
        $otro = User::factory()->create();

        $material->update(['estado' => Material::RETIRADO, 'user_id' => $otro->id]);

        $material->refresh();
        $this->assertSame(Material::DISPONIBLE, $material->estado);
        $this->assertNotSame($otro->id, $material->user_id);
    }

    public function test_el_estado_de_una_solicitud_no_se_asigna_en_masa(): void
    {
        $solicitud = SolicitudRetiro::factory()->create();

        $solicitud->update(['estado' => 'completada', 'recolector_id' => User::factory()->recolector()->create()->id]);

        $solicitud->refresh();
        $this->assertSame('pendiente', $solicitud->estado->value);
        $this->assertNull($solicitud->recolector_id);
    }

    public function test_la_contrasenia_no_aparece_al_convertir_el_usuario(): void
    {
        $array = User::factory()->create()->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }
}
