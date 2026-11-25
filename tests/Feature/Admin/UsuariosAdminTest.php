<?php

namespace Tests\Feature\Admin;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Panel admin (T18) y administración de usuarios y roles (T17).
 */
class UsuariosAdminTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function usuario(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    private function admin(): User
    {
        return $this->usuario('admin@reciclaje.test');
    }

    // ---- Panel (T18) ----

    public function test_el_panel_muestra_los_totales_de_usuarios(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertOk()
            ->assertSee('Usuarios registrados')
            ->assertSee('Usuarios por rol')
            ->assertSee('Usuarios y roles');
    }

    // ---- Acceso ----

    public function test_un_vecino_no_puede_ver_el_listado(): void
    {
        $this->actingAs($this->usuario('laura@reciclaje.test'))
            ->get('/admin/usuarios')
            ->assertForbidden();
    }

    public function test_un_vecino_no_puede_cambiar_roles(): void
    {
        $diego = $this->usuario('diego@reciclaje.test');

        $this->actingAs($this->usuario('laura@reciclaje.test'))
            ->patch("/admin/usuarios/{$diego->id}/rol", ['rol_id' => Rol::ADMIN])
            ->assertForbidden();

        $this->assertSame(Rol::VECINO, $diego->fresh()->rol_id);
    }

    public function test_un_invitado_es_enviado_al_login(): void
    {
        $this->get('/admin/usuarios')->assertRedirect(route('login'));
    }

    // ---- Listado ----

    public function test_el_admin_ve_el_listado_de_usuarios(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/usuarios')
            ->assertOk()
            ->assertSee('laura@reciclaje.test')
            ->assertSee('diego@reciclaje.test')
            ->assertSee('coop@reciclaje.test');
    }

    public function test_busca_por_nombre_o_email(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/usuarios?buscar=laura')
            ->assertOk()
            ->assertSee('laura@reciclaje.test')
            ->assertDontSee('diego@reciclaje.test');
    }

    public function test_filtra_por_rol(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/usuarios?rol='.Rol::RECOLECTOR)
            ->assertOk()
            ->assertSee('coop@reciclaje.test')
            ->assertDontSee('laura@reciclaje.test');
    }

    public function test_filtra_las_cuentas_dadas_de_baja(): void
    {
        $this->usuario('diego@reciclaje.test')->forceFill(['activo' => false])->save();

        $this->actingAs($this->admin())
            ->get('/admin/usuarios?estado=inactivos')
            ->assertOk()
            ->assertSee('diego@reciclaje.test')
            ->assertDontSee('laura@reciclaje.test');
    }

    // ---- Cambio de rol ----

    public function test_el_admin_cambia_el_rol_de_un_usuario(): void
    {
        $laura = $this->usuario('laura@reciclaje.test');

        $this->actingAs($this->admin())
            ->from('/admin/usuarios')
            ->patch("/admin/usuarios/{$laura->id}/rol", ['rol_id' => Rol::RECOLECTOR])
            ->assertRedirect('/admin/usuarios')
            ->assertSessionHas('status');

        $this->assertSame(Rol::RECOLECTOR, $laura->fresh()->rol_id);
    }

    public function test_rechaza_un_rol_que_no_existe(): void
    {
        $laura = $this->usuario('laura@reciclaje.test');

        $this->actingAs($this->admin())
            ->patch("/admin/usuarios/{$laura->id}/rol", ['rol_id' => 999])
            ->assertSessionHasErrors('rol_id');

        $this->assertSame(Rol::VECINO, $laura->fresh()->rol_id);
    }

    public function test_el_admin_no_puede_cambiar_su_propio_rol(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch("/admin/usuarios/{$admin->id}/rol", ['rol_id' => Rol::VECINO])
            ->assertSessionHas('error', 'No podés cambiar tu propio rol.');

        $this->assertSame(Rol::ADMIN, $admin->fresh()->rol_id);
    }

    // ---- Baja lógica ----

    public function test_el_admin_desactiva_y_reactiva_una_cuenta(): void
    {
        $laura = $this->usuario('laura@reciclaje.test');

        $this->actingAs($this->admin())->patch("/admin/usuarios/{$laura->id}/estado");
        $this->assertFalse($laura->fresh()->activo);

        $this->actingAs($this->admin())->patch("/admin/usuarios/{$laura->id}/estado");
        $this->assertTrue($laura->fresh()->activo);
    }

    public function test_desactivar_no_borra_el_usuario(): void
    {
        $laura = $this->usuario('laura@reciclaje.test');

        $this->actingAs($this->admin())->patch("/admin/usuarios/{$laura->id}/estado");

        $this->assertDatabaseHas('users', ['email' => 'laura@reciclaje.test', 'activo' => false]);
    }

    public function test_el_admin_no_puede_desactivarse_a_si_mismo(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch("/admin/usuarios/{$admin->id}/estado")
            ->assertSessionHas('error', 'No podés desactivar tu propia cuenta.');

        $this->assertTrue($admin->fresh()->activo);
    }

    public function test_una_cuenta_desactivada_pierde_la_sesion_en_el_siguiente_clic(): void
    {
        $laura = $this->usuario('laura@reciclaje.test');
        $this->actingAs($laura)->get('/')->assertOk();

        // El admin la desactiva mientras ella tiene la sesión abierta.
        $laura->forceFill(['activo' => false])->save();

        $this->get('/')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
