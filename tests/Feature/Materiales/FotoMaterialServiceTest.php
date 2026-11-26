<?php

namespace Tests\Feature\Materiales;

use App\Services\FotoMaterialService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Fotos de materiales (T19).
 */
class FotoMaterialServiceTest extends TestCase
{
    /** PNG real de 1x1 píxel: no necesita la extensión GD de PHP. */
    private function imagen(string $nombre = 'foto.png'): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($nombre, $png);
    }

    /**
     * Archivo subido REAL (no "fake"): Laravel detecta su tipo leyendo el
     * contenido, igual que en una subida verdadera desde el navegador.
     */
    private function archivoReal(string $nombre, string $contenido): UploadedFile
    {
        $ruta = tempnam(sys_get_temp_dir(), 'subida');
        file_put_contents($ruta, $contenido);

        return new UploadedFile($ruta, $nombre, null, null, true);
    }

    private function valida(UploadedFile $archivo): bool
    {
        return Validator::make(['foto' => $archivo], ['foto' => FotoMaterialService::reglas()])->passes();
    }

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(FotoMaterialService::DISCO);
    }

    public function test_guarda_la_foto_en_la_carpeta_de_materiales(): void
    {
        $ruta = app(FotoMaterialService::class)->guardar($this->imagen());

        $this->assertStringStartsWith('materiales/', $ruta);
        Storage::disk('public')->assertExists($ruta);
    }

    public function test_dos_fotos_con_el_mismo_nombre_no_se_pisan(): void
    {
        $servicio = app(FotoMaterialService::class);

        $this->assertNotSame(
            $servicio->guardar($this->imagen('foto.png')),
            $servicio->guardar($this->imagen('foto.png')),
        );
    }

    public function test_reemplazar_borra_la_foto_anterior(): void
    {
        $servicio = app(FotoMaterialService::class);
        $vieja = $servicio->guardar($this->imagen());

        $nueva = $servicio->reemplazar($vieja, $this->imagen());

        Storage::disk('public')->assertMissing($vieja);
        Storage::disk('public')->assertExists($nueva);
    }

    public function test_eliminar_sin_foto_no_falla(): void
    {
        app(FotoMaterialService::class)->eliminar(null);
        app(FotoMaterialService::class)->eliminar('materiales/no-existe.png');

        $this->assertTrue(true);
    }

    public function test_devuelve_la_url_publica(): void
    {
        $servicio = app(FotoMaterialService::class);

        $this->assertNull($servicio->url(null));
        $this->assertStringContainsString('/storage/materiales/x.png', $servicio->url('materiales/x.png'));
    }

    public function test_acepta_una_imagen_y_la_foto_es_opcional(): void
    {
        $this->assertTrue($this->valida($this->imagen()));
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        $this->assertTrue($this->valida($this->archivoReal('foto-real.png', $png)));
        $this->assertTrue(Validator::make([], ['foto' => FotoMaterialService::reglas()])->passes());
    }

    public function test_rechaza_un_archivo_que_no_es_imagen(): void
    {
        $this->assertFalse($this->valida($this->archivoReal('documento.pdf', "%PDF-1.4\n%contenido de prueba\n")));
    }

    public function test_rechaza_un_php_renombrado_como_jpg(): void
    {
        // Un script PHP con nombre de foto: el contenido no es una imagen.
        $this->assertFalse($this->valida($this->archivoReal('foto.jpg', '<?php echo "hola"; ?>')));
    }

    public function test_rechaza_una_foto_de_mas_de_2_mb(): void
    {
        $this->assertFalse($this->valida($this->imagen()->size(3000)));
    }
}
