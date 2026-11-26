<?php

namespace Tests\Feature\Auth;

use App\Models\Barrio;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Registro de vecinos (T03 / HU1 / RF01).
 */
class RegistroTest extends TestCase
{
    use RefreshDatabase;

    /** Carga roles, permisos, barrios y usuarios de prueba antes de cada test. */
    protected $seed = true;

    private function datosValidos(array $cambios = []): array
    {
        return array_merge([
            'name' => 'Ana',
            'apellido' => 'Gómez',
            'barrio_id' => Barrio::where('nombre', 'Centro')->value('id'),
            'email' => 'ana@mail.com',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ], $cambios);
    }

    public function test_muestra_el_formulario_con_los_barrios(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Crear Cuenta de Vecino')
            ->assertSee('San Miguel');
    }

    public function test_registra_un_vecino_e_inicia_su_sesion(): void
    {
        $this->post('/register', $this->datosValidos())
            ->assertRedirect(route('home'));

        $user = User::where('email', 'ana@mail.com')->first();

        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $this->assertSame(Rol::VECINO, $user->rol_id);
        $this->assertSame('Gómez', $user->apellido);
        $this->assertTrue(Hash::check('secreto123', $user->password));
        $this->assertNotSame('secreto123', $user->password);
    }

    public function test_guarda_el_email_en_minusculas(): void
    {
        $this->post('/register', $this->datosValidos(['email' => '  Ana@Mail.COM ']));

        $this->assertDatabaseHas('users', ['email' => 'ana@mail.com']);
    }

    public function test_rechaza_un_email_ya_registrado(): void
    {
        $this->post('/register', $this->datosValidos(['email' => 'laura@reciclaje.test']))
            ->assertSessionHasErrors(['email' => 'El email ya está registrado.']);

        $this->assertGuest();
    }

    public function test_rechaza_un_email_duplicado_con_mayusculas(): void
    {
        $this->post('/register', $this->datosValidos(['email' => 'LAURA@reciclaje.test']))
            ->assertSessionHasErrors('email');
    }

    public function test_rechaza_contrasenas_que_no_coinciden(): void
    {
        $this->post('/register', $this->datosValidos(['password_confirmation' => 'otra-cosa']))
            ->assertSessionHasErrors(['password' => 'Las contraseñas no coinciden.']);
    }

    public function test_rechaza_una_contrasena_corta(): void
    {
        $this->post('/register', $this->datosValidos(['password' => '123', 'password_confirmation' => '123']))
            ->assertSessionHasErrors('password');
    }

    public function test_rechaza_campos_obligatorios_vacios(): void
    {
        $this->post('/register', [])
            ->assertSessionHasErrors(['name', 'apellido', 'barrio_id', 'email', 'password']);

        $this->assertGuest();
    }

    public function test_rechaza_un_barrio_que_no_existe(): void
    {
        $this->post('/register', $this->datosValidos(['barrio_id' => 99999]))
            ->assertSessionHasErrors(['barrio_id' => 'Elegí un barrio de la lista.']);
    }

    public function test_no_permite_registrarse_como_administrador(): void
    {
        // Alguien agrega rol_id=1 a mano en la petición.
        $this->post('/register', $this->datosValidos(['rol_id' => Rol::ADMIN]));

        $this->assertSame(Rol::VECINO, User::where('email', 'ana@mail.com')->value('rol_id'));
    }

    public function test_un_usuario_con_sesion_no_ve_el_registro(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/register')
            ->assertRedirect(route('home'));
    }
}
