<div class="space-y-6 bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-lg">

    <fieldset class="border border-gray-600 rounded-lg p-4">
        <legend class="px-2 text-lg font-semibold text-gray-300">Datos del Jugador</legend>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-2">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-400 mb-1">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $player->nombre) }}" required
                    class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
            </div>
            <div>
                <label for="numero" class="block text-sm font-medium text-gray-400 mb-1">Número</label>
                <input type="number" name="numero" id="numero" value="{{ old('numero', $player->numero) }}" required min="1"
                    max="99" class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600
                                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
            </div>
        </div>
    </fieldset>

    <fieldset class="border border-gray-600 rounded-lg p-4">
        <legend class="px-2 text-lg font-semibold text-gray-300">Equipo y Posición</legend>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-2">
            <div>
                <label for="equipo_id" class="block text-sm font-medium text-gray-400 mb-1">Equipo</label>
                <select name="equipo_id" id="equipo_id" required 
                    class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    <option value="">Seleccionar Equipo...</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @selected(old('equipo_id', $player->equipo_id) == $team->id)>{{ $team->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="player-position" class="block text-sm font-medium text-gray-400 mb-1">Posición</label>
                <input type="hidden" name="posicion_general" id="posicion_general_input" value="{{ $player->posicion_general }}">
                <select name="posicion_especifica" id="player-position" required
                    class="mt-1 w-full px-3 py-2 bg-gray-700 rounded-md text-white border border-gray-600
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    <option value="">Seleccionar Posición...</option>
                    @foreach ($positions as $general => $specifics)
                        <optgroup label="{{ $general }}">
                            @foreach ($specifics as $abbr => $name)
                                <option value="{{ $abbr }}" data-general="{{ strtolower($general) }}" @selected(old('posicion_especifica', $player->posicion_especifica) == $abbr)>
                                    {{ $name }} ({{ $abbr }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
        </div>
    </fieldset>

    <fieldset class="border border-gray-600 rounded-lg p-4">
        <legend class="px-2 text-lg font-semibold text-gray-300">Fotografía</legend>
        <div class="mt-4 flex flex-col sm:flex-row items-center gap-6">
            <div class="flex-shrink-0">
                <p class="text-sm font-medium text-gray-400 mb-2">Vista Previa</p>
                <img id="image-preview" src="{{ $player->foto_url ?? 'https://placehold.co/100x100/1f2937/FFFFFF?text=JUGADOR' }}"
                    class="w-24 h-24 rounded-full object-cover border-4 border-gray-600">
            </div>
            
            <div class="flex-grow w-full">
                <label for="photo-upload" class="block text-sm font-medium text-gray-400 mb-2">Subir Nueva Foto (Sustituir)</label>
                <label for="photo-upload" class="w-full flex items-center justify-center px-4 py-3 bg-gray-700 border-2 border-dashed border-gray-500 rounded-lg cursor-pointer hover:bg-gray-600 hover:border-blue-500 transition">
                    <svg class="w-6 h-6 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span id="file-name" class="text-gray-400">Seleccionar un archivo...</span>
                </label>
                <input id="photo-upload" type="file" name="photo" accept="image/*" class="hidden">
            </div>
        </div>
    </fieldset>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Lógica para la posición (existente) ---
        const positionSelect = document.getElementById('player-position');
        const generalPositionInput = document.getElementById('posicion_general_input');
        function updateGeneralPosition() {
            const selectedOption = positionSelect.options[positionSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                generalPositionInput.value = selectedOption.dataset.general;
            } else {
                generalPositionInput.value = '';
            }
        }
        updateGeneralPosition();
        positionSelect.addEventListener('change', updateGeneralPosition);

        // --- Nueva lógica para la vista previa de la imagen ---
        const photoUpload = document.getElementById('photo-upload');
        const imagePreview = document.getElementById('image-preview');
        const fileNameSpan = document.getElementById('file-name');

        photoUpload.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                // Actualizar la vista previa de la imagen
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);

                // Actualizar el nombre del archivo
                fileNameSpan.textContent = file.name;
            } else {
                // Si el usuario cancela, se revierte al estado inicial
                imagePreview.src = "{{ $player->foto_url ?? 'https://placehold.co/100x100/1f2937/FFFFFF?text=JUGADOR' }}";
                fileNameSpan.textContent = "Seleccionar un archivo...";
            }
        });
    });
</script>