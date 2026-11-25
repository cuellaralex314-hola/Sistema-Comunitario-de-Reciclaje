<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Fotos de los materiales publicados (T19 / HU6 / RF06).
 *
 * Uso desde el controlador de materiales (Fabián):
 *
 *   // En el FormRequest:
 *   'foto' => FotoMaterialService::reglas(),
 *
 *   // Al crear:
 *   $datos['foto_path'] = $request->hasFile('foto')
 *       ? $fotos->guardar($request->file('foto'))
 *       : null;
 *
 *   // Al editar (borra la foto anterior):
 *   if ($request->hasFile('foto')) {
 *       $datos['foto_path'] = $fotos->reemplazar($material->foto_path, $request->file('foto'));
 *   }
 *
 *   // En la vista:
 *   <img src="{{ app(\App\Services\FotoMaterialService::class)->url($material->foto_path) }}">
 *
 * El formulario necesita enctype="multipart/form-data".
 */
class FotoMaterialService
{
    /** Disco público: los archivos quedan en storage/app/public y se sirven por /storage. */
    public const DISCO = 'public';

    /** Carpeta dentro del disco. */
    public const CARPETA = 'materiales';

    /** Tamaño máximo en kilobytes (2 MB). */
    public const MAX_KB = 2048;

    /**
     * Reglas de validación para el campo "foto" (opcional).
     * Solo imágenes reales: la regla "image" revisa el contenido del archivo,
     * no solo su extensión, así no se puede subir un .php renombrado a .jpg.
     */
    public static function reglas(): array
    {
        return ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::MAX_KB];
    }

    /**
     * Mensajes en español para usar en el messages() del FormRequest.
     */
    public static function mensajes(string $campo = 'foto'): array
    {
        return [
            "{$campo}.image" => 'La foto tiene que ser una imagen.',
            "{$campo}.mimes" => 'La foto tiene que ser JPG, PNG o WEBP.',
            "{$campo}.max" => 'La foto no puede pesar más de 2 MB.',
        ];
    }

    /**
     * Guarda la foto con un nombre aleatorio y devuelve su ruta relativa
     * (lo que se guarda en materiales.foto_path).
     */
    public function guardar(UploadedFile $foto): string
    {
        return $foto->store(self::CARPETA, self::DISCO);
    }

    /**
     * Guarda la foto nueva y borra la anterior, si había.
     */
    public function reemplazar(?string $rutaAnterior, UploadedFile $nueva): string
    {
        $rutaNueva = $this->guardar($nueva);
        $this->eliminar($rutaAnterior);

        return $rutaNueva;
    }

    /**
     * Borra una foto. Si la ruta es nula o el archivo no existe, no hace nada.
     */
    public function eliminar(?string $ruta): void
    {
        if ($ruta && Storage::disk(self::DISCO)->exists($ruta)) {
            Storage::disk(self::DISCO)->delete($ruta);
        }
    }

    /**
     * URL pública de la foto, o null si el material no tiene foto.
     */
    public function url(?string $ruta): ?string
    {
        return $ruta ? Storage::disk(self::DISCO)->url($ruta) : null;
    }
}
