<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Control de acceso por rol y permisos (T05 / HU3 / RF03).
 */
class AccesoPorRolTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function usuario(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    public function test_un_invitado_es_enviado_al_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_un_vecino_recibe_403_en_el_panel_admin(): void
    {
        $this->actingAs($this->usuario('laura@reciclaje.test'))
            ->get('/admin')
            ->assertForbidden()
            ->assertSee('Acceso denegado');
    }

    public function test_un_recolector_recibe_403_en_el_panel_admin(): void
    {
        $this->actingAs($this->usuario('coop@reciclaje.test'))
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_el_administrador_entra_al_panel(): void
    {
        $this->actingAs($this->usuario('admin@reciclaje.test'))
            ->get('/admin')
            ->assertOk()
            ->assertSee('Panel de administración')
            ->assertSee('Usuarios y roles');
    }

    public function test_los_permisos_de_cada_rol(): void
    {
        $admin = $this->usuario('admin@reciclaje.test');
        $vecino = $this->usuario('laura@reciclaje.test');
        $recolector = $this->usuario('coop@reciclaje.test');

        $this->assertTrue(Gate::forUser($admin)->allows('usuarios.gestionar'));
        $this->assertTrue(Gate::forUser($admin)->allows('estadisticas.ver'));
        $this->assertFalse(Gate::forUser($admin)->allows('materiales.publicar'));

        $this->assertTrue(Gate::forUser($vecino)->allows('materiales.publicar'));
        $this->assertFalse(Gate::forUser($vecino)->allows('usuarios.gestionar'));

        $this->assertTrue(Gate::forUser($recolector)->allows('solicitudes.actualizar'));
        $this->assertFalse(Gate::forUser($recolector)->allows('solicitudes.gestionar'));
    }

    public function test_solo_el_administrador_ve_el_enlace_al_panel(): void
    {
        $this->actingAs($this->usuario('admin@reciclaje.test'))
            ->get('/')
            ->assertSee('Panel admin');

        $this->actingAs($this->usuario('laura@reciclaje.test'))
            ->get('/')
            ->assertDontSee('Panel admin');
    }
}
