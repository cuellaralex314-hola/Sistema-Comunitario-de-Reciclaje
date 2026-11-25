<?php

namespace Tests\Feature\Solicitudes;

use App\Enums\EstadoSolicitud;
use App\Models\SolicitudRetiro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rol recolector y acciones del administrador por HTTP (T33, T34).
 */
class RetirosRecolectorTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private User $admin;

    private User $recolector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->recolector = User::factory()->recolector()->create();
    }

    // --- Listado del recolector ---------------------------------------------

    public function test_el_recolector_ve_solo_sus_retiros(): void
    {
        SolicitudRetiro::factory()->enProceso($this->recolector)->conMateriales(1)->create(['direccion' => 'Av. Mío 100']);
        SolicitudRetiro::factory()->enProceso()->conMateriales(1)->create(['direccion' => 'Calle Ajena 200']);

        $this->actingAs($this->recolector)
            ->get(route('recolector.retiros.index'))
            ->assertOk()
            ->assertSee('Av. Mío 100')
            ->assertDontSee('Calle Ajena 200');
    }

    public function test_un_vecino_no_entra_a_la_seccion_del_recolector(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('recolector.retiros.index'))
            ->assertForbidden();
    }

    public function test_un_invitado_va_al_login(): void
    {
        $this->get(route('recolector.retiros.index'))->assertRedirect(route('login'));
    }

    // --- Completar ----------------------------------------------------------

    public function test_el_recolector_completa_su_retiro(): void
    {
        $solicitud = SolicitudRetiro::factory()->enProceso($this->recolector)->conMateriales(1)->create();
        $id = $solicitud->materiales()->value('materiales.id');

        $this->actingAs($this->recolector)
            ->patch(route('recolector.retiros.completar', $solicitud), ['pesos' => [$id => '2.75']])
            ->assertRedirect(route('recolector.retiros.index'))
            ->assertSessionHas('status');

        $this->assertSame(EstadoSolicitud::Completada, $solicitud->fresh()->estado);
    }

    public function test_un_recolector_no_completa_un_retiro_ajeno(): void
    {
        $solicitud = SolicitudRetiro::factory()->enProceso()->conMateriales(1)->create();
        $id = $solicitud->materiales()->value('materiales.id');

        $this->actingAs($this->recolector)
            ->patch(route('recolector.retiros.completar', $solicitud), ['pesos' => [$id => 1]])
            ->assertForbidden();

        $this->assertSame(EstadoSolicitud::EnProceso, $solicitud->fresh()->estado);
    }

    public function test_completar_sin_pesos_muestra_error(): void
    {
        $solicitud = SolicitudRetiro::factory()->enProceso($this->recolector)->conMateriales(1)->create();

        $this->actingAs($this->recolector)
            ->from(route('recolector.retiros.index'))
            ->patch(route('recolector.retiros.completar', $solicitud), ['pesos' => []])
            ->assertSessionHasErrors('pesos');

        $this->assertSame(EstadoSolicitud::EnProceso, $solicitud->fresh()->estado);
    }

    // --- Administrador ------------------------------------------------------

    public function test_el_admin_asigna_un_recolector(): void
    {
        $solicitud = SolicitudRetiro::factory()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.solicitudes.asignar', $solicitud), ['recolector_id' => $this->recolector->id])
            ->assertSessionHas('status');

        $solicitud->refresh();
        $this->assertSame(EstadoSolicitud::EnProceso, $solicitud->estado);
        $this->assertSame($this->recolector->id, $solicitud->recolector_id);
    }

    public function test_no_se_puede_asignar_a_un_vecino(): void
    {
        $solicitud = SolicitudRetiro::factory()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.solicitudes.asignar', $solicitud), ['recolector_id' => User::factory()->create()->id])
            ->assertSessionHasErrors('recolector_id');

        $this->assertSame(EstadoSolicitud::Pendiente, $solicitud->fresh()->estado);
    }

    public function test_asignar_dos_veces_muestra_el_error_de_transicion(): void
    {
        $solicitud = SolicitudRetiro::factory()->enProceso($this->recolector)->create();

        $this->actingAs($this->admin)
            ->from('/admin')
            ->patch(route('admin.solicitudes.asignar', $solicitud), ['recolector_id' => $this->recolector->id])
            ->assertRedirect('/admin')
            ->assertSessionHasErrors('solicitud');
    }

    public function test_el_admin_rechaza_con_motivo(): void
    {
        $solicitud = SolicitudRetiro::factory()->conMateriales(1)->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.solicitudes.rechazar', $solicitud), ['motivo' => 'Fuera de la zona de cobertura'])
            ->assertSessionHas('status');

        $this->assertSame(EstadoSolicitud::Rechazada, $solicitud->fresh()->estado);
    }

    public function test_rechazar_sin_motivo_muestra_error(): void
    {
        $solicitud = SolicitudRetiro::factory()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.solicitudes.rechazar', $solicitud), ['motivo' => '  '])
            ->assertSessionHasErrors('motivo');

        $this->assertSame(EstadoSolicitud::Pendiente, $solicitud->fresh()->estado);
    }

    public function test_un_vecino_ni_un_recolector_pueden_asignar(): void
    {
        $solicitud = SolicitudRetiro::factory()->create();

        foreach ([User::factory()->create(), $this->recolector] as $usuario) {
            $this->actingAs($usuario)
                ->patch(route('admin.solicitudes.asignar', $solicitud), ['recolector_id' => $this->recolector->id])
                ->assertForbidden();
        }

        $this->assertSame(EstadoSolicitud::Pendiente, $solicitud->fresh()->estado);
    }
}
