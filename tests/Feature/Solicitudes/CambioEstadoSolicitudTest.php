<?php

namespace Tests\Feature\Solicitudes;

use App\Enums\EstadoSolicitud;
use App\Events\SolicitudCambioDeEstado;
use App\Exceptions\TransicionInvalidaException;
use App\Models\Material;
use App\Models\SolicitudRetiro;
use App\Models\User;
use App\Services\CambioEstadoSolicitudService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use LogicException;
use Tests\TestCase;

/**
 * Servicio de cambio de estados (T31), historial (T32) y sus reglas (T34).
 */
class CambioEstadoSolicitudTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private CambioEstadoSolicitudService $estados;

    private User $admin;

    private User $recolector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->estados = app(CambioEstadoSolicitudService::class);
        $this->admin = User::factory()->admin()->create();
        $this->recolector = User::factory()->recolector()->create();
    }

    private function estadosDeMateriales(SolicitudRetiro $solicitud): array
    {
        return $solicitud->materiales()->pluck('estado')->unique()->values()->all();
    }

    // --- Tabla de transiciones -------------------------------------------

    public function test_las_transiciones_permitidas_son_las_del_disenio(): void
    {
        $this->assertTrue(EstadoSolicitud::Pendiente->puedePasarA(EstadoSolicitud::EnProceso));
        $this->assertTrue(EstadoSolicitud::Pendiente->puedePasarA(EstadoSolicitud::Cancelada));
        $this->assertTrue(EstadoSolicitud::Pendiente->puedePasarA(EstadoSolicitud::Rechazada));
        $this->assertTrue(EstadoSolicitud::EnProceso->puedePasarA(EstadoSolicitud::Completada));
        $this->assertTrue(EstadoSolicitud::EnProceso->puedePasarA(EstadoSolicitud::Rechazada));

        $this->assertFalse(EstadoSolicitud::Pendiente->puedePasarA(EstadoSolicitud::Completada));
        $this->assertFalse(EstadoSolicitud::EnProceso->puedePasarA(EstadoSolicitud::Cancelada));
        $this->assertFalse(EstadoSolicitud::EnProceso->puedePasarA(EstadoSolicitud::Pendiente));

        foreach ([EstadoSolicitud::Completada, EstadoSolicitud::Cancelada, EstadoSolicitud::Rechazada] as $final) {
            $this->assertTrue($final->esFinal(), $final->value);
        }
    }

    // --- Asignar ------------------------------------------------------------

    public function test_asignar_pasa_la_solicitud_a_en_proceso_y_registra_el_historial(): void
    {
        $solicitud = SolicitudRetiro::factory()->conMateriales()->create();

        $this->estados->asignar($solicitud, $this->recolector, $this->admin);

        $solicitud->refresh();
        $this->assertSame(EstadoSolicitud::EnProceso, $solicitud->estado);
        $this->assertSame($this->recolector->id, $solicitud->recolector_id);
        $this->assertSame([Material::RESERVADO], $this->estadosDeMateriales($solicitud));
        $this->assertDatabaseHas('historial_estados', [
            'solicitud_id' => $solicitud->id,
            'user_id' => $this->admin->id,
            'estado_anterior' => 'pendiente',
            'estado_nuevo' => 'en_proceso',
        ]);
    }

    public function test_no_se_asigna_a_un_usuario_que_no_es_recolector(): void
    {
        $solicitud = SolicitudRetiro::factory()->create();
        $vecino = User::factory()->create();

        try {
            $this->estados->asignar($solicitud, $vecino, $this->admin);
            $this->fail('Se esperaba TransicionInvalidaException');
        } catch (TransicionInvalidaException) {
        }

        $this->assertSame(EstadoSolicitud::Pendiente, $solicitud->fresh()->estado);
        $this->assertSame(0, $solicitud->historial()->count());
    }

    public function test_no_se_asigna_a_un_recolector_desactivado(): void
    {
        $solicitud = SolicitudRetiro::factory()->create();
        $inactivo = User::factory()->recolector()->inactivo()->create();

        $this->expectException(TransicionInvalidaException::class);

        $this->estados->asignar($solicitud, $inactivo, $this->admin);
    }

    // --- Completar ----------------------------------------------------------

    public function test_completar_guarda_los_pesos_y_retira_los_materiales(): void
    {
        $solicitud = SolicitudRetiro::factory()->enProceso($this->recolector)->conMateriales(2)->create();
        [$a, $b] = $solicitud->materiales()->pluck('materiales.id')->all();

        $this->estados->completar($solicitud, [$a => '3.5', $b => 1.25], $this->recolector);

        $solicitud->refresh();
        $this->assertSame(EstadoSolicitud::Completada, $solicitud->estado);
        $this->assertNotNull($solicitud->completada_at);
        $this->assertSame([Material::RETIRADO], $this->estadosDeMateriales($solicitud));
        $this->assertEquals(3.5, $solicitud->materiales()->find($a)->pivot->peso_real_kg);
        $this->assertEquals(1.25, $solicitud->materiales()->find($b)->pivot->peso_real_kg);
    }

    public function test_no_se_completa_una_solicitud_pendiente(): void
    {
        $solicitud = SolicitudRetiro::factory()->conMateriales(1)->create();
        $id = $solicitud->materiales()->value('materiales.id');

        $this->expectException(TransicionInvalidaException::class);

        $this->estados->completar($solicitud, [$id => 2], $this->admin);
    }

    public function test_si_falta_un_peso_no_se_cambia_nada(): void
    {
        $solicitud = SolicitudRetiro::factory()->enProceso($this->recolector)->conMateriales(2)->create();
        $primero = $solicitud->materiales()->value('materiales.id');

        try {
            $this->estados->completar($solicitud, [$primero => 2], $this->recolector);
            $this->fail('Se esperaba TransicionInvalidaException');
        } catch (TransicionInvalidaException) {
        }

        // La transacción deshizo todo.
        $solicitud->refresh();
        $this->assertSame(EstadoSolicitud::EnProceso, $solicitud->estado);
        $this->assertSame([Material::RESERVADO], $this->estadosDeMateriales($solicitud));
        $this->assertSame(0, $solicitud->materiales()->whereNotNull('peso_real_kg')->count());
        $this->assertSame(0, $solicitud->historial()->count());
    }

    public function test_no_se_aceptan_pesos_de_materiales_ajenos_a_la_solicitud(): void
    {
        $solicitud = SolicitudRetiro::factory()->enProceso($this->recolector)->conMateriales(1)->create();
        $propio = $solicitud->materiales()->value('materiales.id');
        $ajeno = Material::factory()->create()->id;

        $this->expectException(TransicionInvalidaException::class);

        $this->estados->completar($solicitud, [$propio => 1, $ajeno => 1], $this->recolector);
    }

    public function test_no_se_aceptan_pesos_negativos(): void
    {
        $solicitud = SolicitudRetiro::factory()->enProceso($this->recolector)->conMateriales(1)->create();
        $id = $solicitud->materiales()->value('materiales.id');

        $this->expectException(TransicionInvalidaException::class);

        $this->estados->completar($solicitud, [$id => -1], $this->recolector);
    }

    // --- Cancelar -----------------------------------------------------------

    public function test_cancelar_una_pendiente_libera_los_materiales(): void
    {
        $solicitud = SolicitudRetiro::factory()->conMateriales(2)->create();

        $this->estados->cancelar($solicitud, $solicitud->vecino);

        $solicitud->refresh();
        $this->assertSame(EstadoSolicitud::Cancelada, $solicitud->estado);
        $this->assertSame([Material::DISPONIBLE], $this->estadosDeMateriales($solicitud));
    }

    public function test_no_se_cancela_una_solicitud_en_proceso(): void
    {
        $solicitud = SolicitudRetiro::factory()->enProceso($this->recolector)->conMateriales(1)->create();

        try {
            $this->estados->cancelar($solicitud, $solicitud->vecino);
            $this->fail('Se esperaba TransicionInvalidaException');
        } catch (TransicionInvalidaException $e) {
            $this->assertSame('No se puede pasar una solicitud de «En proceso» a «Cancelada».', $e->getMessage());
        }

        $this->assertSame(EstadoSolicitud::EnProceso, $solicitud->fresh()->estado);
        $this->assertSame([Material::RESERVADO], $this->estadosDeMateriales($solicitud));
    }

    // --- Rechazar -----------------------------------------------------------

    public function test_rechazar_exige_motivo(): void
    {
        $solicitud = SolicitudRetiro::factory()->create();

        $this->expectException(TransicionInvalidaException::class);

        $this->estados->rechazar($solicitud, '   ', $this->admin);
    }

    public function test_rechazar_una_en_proceso_guarda_el_motivo_y_libera_los_materiales(): void
    {
        $solicitud = SolicitudRetiro::factory()->enProceso($this->recolector)->conMateriales(1)->create();

        $this->estados->rechazar($solicitud, 'La dirección no existe', $this->admin);

        $solicitud->refresh();
        $this->assertSame(EstadoSolicitud::Rechazada, $solicitud->estado);
        $this->assertSame('La dirección no existe', $solicitud->motivo_rechazo);
        $this->assertSame([Material::DISPONIBLE], $this->estadosDeMateriales($solicitud));
    }

    // --- Estados finales, historial y eventos -------------------------------

    public function test_una_solicitud_completada_ya_no_cambia(): void
    {
        $solicitud = SolicitudRetiro::factory()->enProceso($this->recolector)->conMateriales(1)->create();
        $id = $solicitud->materiales()->value('materiales.id');
        $this->estados->completar($solicitud, [$id => 2], $this->recolector);

        $this->expectException(TransicionInvalidaException::class);

        $this->estados->rechazar($solicitud, 'Tarde', $this->admin);
    }

    public function test_el_historial_guarda_todo_el_recorrido_en_orden(): void
    {
        $solicitud = SolicitudRetiro::factory()->conMateriales(1)->create();
        $vecino = $solicitud->vecino;
        $id = $solicitud->materiales()->value('materiales.id');

        $this->estados->registrarCreacion($solicitud, $vecino);
        $this->estados->asignar($solicitud, $this->recolector, $this->admin);
        $this->estados->completar($solicitud, [$id => 4], $this->recolector);

        $pasos = $solicitud->historial()->get(['estado_anterior', 'estado_nuevo', 'user_id'])
            ->map(fn ($h) => [$h->estado_anterior, $h->estado_nuevo, $h->user_id])
            ->all();

        $this->assertSame([
            [null, 'pendiente', $vecino->id],
            ['pendiente', 'en_proceso', $this->admin->id],
            ['en_proceso', 'completada', $this->recolector->id],
        ], $pasos);
    }

    public function test_el_historial_no_se_puede_modificar(): void
    {
        $solicitud = SolicitudRetiro::factory()->create();
        $registro = $this->estados->registrarCreacion($solicitud, $solicitud->vecino);

        $this->expectException(LogicException::class);

        $registro->update(['comentario' => 'cambiado']);
    }

    public function test_cada_cambio_dispara_el_evento_para_los_avisos(): void
    {
        Event::fake([SolicitudCambioDeEstado::class]);
        $solicitud = SolicitudRetiro::factory()->create();

        $this->estados->asignar($solicitud, $this->recolector, $this->admin);

        Event::assertDispatched(SolicitudCambioDeEstado::class, fn ($e) => $e->solicitud->is($solicitud)
            && $e->anterior === EstadoSolicitud::Pendiente
            && $e->nuevo === EstadoSolicitud::EnProceso
            && $e->actor->is($this->admin));
    }

    public function test_un_cambio_invalido_no_dispara_el_evento(): void
    {
        Event::fake([SolicitudCambioDeEstado::class]);
        $solicitud = SolicitudRetiro::factory()->create();

        try {
            $this->estados->completar($solicitud, [], $this->recolector);
        } catch (TransicionInvalidaException) {
        }

        Event::assertNotDispatched(SolicitudCambioDeEstado::class);
    }
}
