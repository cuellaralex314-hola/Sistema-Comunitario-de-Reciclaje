<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Inicio y cierre de sesión (T04 / HU2 / RF02).
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function laura(): User
    {
        return User::where('email', 'laura@reciclaje.test')->firstOrFail();
    }

    public function test_muestra_el_formulario_de_login(): void
    {
        $this->get('/login')->assertOk()->assertSee('Iniciar Sesión');
    }

    public function test_inicia_sesion_con_datos_correctos(): void
    {
        $this->post('/login', ['email' => 'laura@reciclaje.test', 'password' => 'password'])
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($this->laura());
    }

    public function test_el_email_no_distingue_mayusculas(): void
    {
        $this->post('/login', ['email' => 'Laura@Reciclaje.TEST', 'password' => 'password']);

        $this->assertAuthenticatedAs($this->laura());
    }

    public function test_rechaza_una_contrasena_incorrecta(): void
    {
        $this->post('/login', ['email' => 'laura@reciclaje.test', 'password' => 'incorrecta'])
            ->assertSessionHasErrors(['email' => 'El correo o la contraseña son incorrectos.']);

        $this->assertGuest();
    }

    public function test_rechaza_un_email_inexistente_con_el_mismo_mensaje(): void
    {
        $this->post('/login', ['email' => 'nadie@reciclaje.test', 'password' => 'password'])
            ->assertSessionHasErrors(['email' => 'El correo o la contraseña son incorrectos.']);
    }

    public function test_una_cuenta_desactivada_no_puede_entrar(): void
    {
        $this->laura()->forceFill(['activo' => false])->save();

        $this->post('/login', ['email' => 'laura@reciclaje.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_bloquea_despues_de_cinco_intentos_fallidos(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'laura@reciclaje.test', 'password' => 'incorrecta']);
        }

        // El sexto intento se bloquea aunque la contraseña sea correcta.
        $respuesta = $this->post('/login', ['email' => 'laura@reciclaje.test', 'password' => 'password']);

        $respuesta->assertSessionHasErrors('email');
        $this->assertStringContainsString('Demasiados intentos', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_cierra_la_sesion(): void
    {
        $this->actingAs($this->laura())
            ->post('/logout')
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_un_invitado_vuelve_a_la_pagina_que_queria_despues_del_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));

        $this->post('/login', ['email' => 'admin@reciclaje.test', 'password' => 'password'])
            ->assertRedirect('/admin');
    }

    public function test_el_menu_muestra_cerrar_sesion_con_la_sesion_iniciada(): void
    {
        $this->actingAs($this->laura())
            ->get('/')
            ->assertSee('Cerrar sesión')
            ->assertDontSee('Registrarse');
    }
}
