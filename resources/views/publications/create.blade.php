@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Encabezado -->
    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center text-sm text-green-600 hover:text-green-700 font-medium mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Publicar Material Reciclable</h1>
        <p class="text-gray-600 text-sm mt-1">Completa los datos para que un recuperador urbano o vecino pueda coordinar el retiro.</p>
    </div>

    <!-- Contenedor del Formulario -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="#" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Título de la publicación -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Título de la publicación <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" required
                    placeholder="Ej: Lote de botellas PET limpias y aplastadas"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
            </div>

            <!-- Categoría y Cantidad (en 2 columnas en escritorio) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Categoría -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Categoría de material <span class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="" disabled selected>Selecciona una categoría</option>
                        <option value="1">Plásticos (PET, PEAD, envases)</option>
                        <option value="2">Papel y Cartón</option>
                        <option value="3">Vidrio (botellas y frascos)</option>
                        <option value="4">Metales (aluminio, latas, chatarra)</option>
                        <option value="5">Residuos Electrónicos (RAEE)</option>
                        <option value="6">Aceite Vegetal Usado</option>
                    </select>
                </div>

                <!-- Cantidad estimada y Unidad -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Cantidad estimada <span class="text-red-500">*</span></label>
                    <div class="mt-1 flex rounded-lg shadow-sm">
                        <input type="number" name="quantity" id="quantity" min="1" step="0.5" required
                            placeholder="Ej: 5"
                            class="block w-full rounded-none rounded-l-lg border-gray-300 focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <select name="unit" id="unit"
                            class="inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-600 sm:text-sm focus:border-green-500 focus:ring-green-500">
                            <option value="kg">Kilogramos (kg)</option>
                            <option value="unidades">Unidades</option>
                            <option value="bolsas">Bolsas</option>
                            <option value="cajas">Cajas</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Descripción y Estado del material -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Descripción y estado del material <span class="text-red-500">*</span></label>
                <textarea id="description" name="description" rows="3" required
                    placeholder="Detalla si los materiales están limpios, secos, separados o alguna indicación especial..."
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"></textarea>
                <p class="mt-1 text-xs text-gray-500">Recuerda que los materiales limpios y secos tienen mayor aprovechamiento.</p>
            </div>

            <!-- Ubicación y Disponibilidad Horaria -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2 border-t border-gray-100">
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Dirección o Barrio de retiro <span class="text-red-500">*</span></label>
                    <input type="text" name="address" id="address" required
                        placeholder="Ej: Barrio San Martín, Av. 25 de Mayo al 800"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="availability" class="block text-sm font-medium text-gray-700">Disponibilidad horaria</label>
                    <input type="text" name="availability" id="availability"
                        placeholder="Ej: Lunes a viernes de 17:00 a 20:00 hs"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>
            </div>

            <!-- Subida de Fotografía con Vista Previa -->
            <div class="pt-2 border-t border-gray-100">
                <label class="block text-sm font-medium text-gray-700 mb-2">Fotografía del material (opcional)</label>
                
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-400 transition" id="drop-area">
                    <div class="space-y-1 text-center" id="upload-placeholder">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none">
                                <span>Sube una imagen</span>
                                <input id="image" name="image" type="file" accept="image/*" class="sr-only">
                            </label>
                            <p class="pl-1">o arrastra y suelta aquí</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, WEBP hasta 5MB</p>
                    </div>

                    <!-- Contenedor de vista previa (oculto por defecto) -->
                    <div id="preview-container" class="hidden relative">
                        <img id="image-preview" src="#" alt="Vista previa" class="max-h-48 rounded-lg object-cover">
                        <button type="button" id="remove-image" class="mt-2 text-xs text-red-600 hover:text-red-800 font-medium underline block mx-auto">
                            Quitar imagen
                        </button>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('home') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-green-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm transition">
                    Publicar Material
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script nativo para previsualizar la imagen subida -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const imageInput = document.getElementById('image');
        const placeholder = document.getElementById('upload-placeholder');
        const previewContainer = document.getElementById('preview-container');
        const imagePreview = document.getElementById('image-preview');
        const removeButton = document.getElementById('remove-image');

        if (imageInput) {
            imageInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        imagePreview.src = event.target.result;
                        placeholder.classList.add('hidden');
                        previewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });

            removeButton.addEventListener('click', function () {
                imageInput.value = '';
                imagePreview.src = '#';
                previewContainer.classList.add('hidden');
                placeholder.classList.remove('hidden');
            });
        }
    });
</script>
@endsection