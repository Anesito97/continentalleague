<div id="teams-content" class="admin-content space-y-6">
    <div class="card p-6">
        <h4 class="text-2xl font-semibold mb-4 text-green-400">Registrar Equipo</h4>
        <form method="POST" action="{{ route('teams.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="team-name" class="block text-sm font-medium text-gray-400">Nombre del Equipo</label>
                <input type="text" name="name" id="team-name" required
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
            </div>
            <div class="mb-4">
                <label for="team-logo" class="block text-sm font-medium text-gray-400">Logo</label>
                <input type="file" name="logo" id="team-logo" accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            </div>
            {{-- Achievements field is omitted as per original crud.js correction, but can be added here if needed --}}
            <button type="submit" id="save-team-btn"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">Guardar
                Equipo </button>
        </form>
    </div>
    <div class="card p-4 hover:transform-none">
        <h4 class="text-xl font-semibold mb-3">Equipos Registrados (DB)</h4>
        <ul id="teams-list" class="space-y-2 text-sm text-gray-300">
            @forelse($teams as $team)
                {{-- Contenido principal de la lista --}}
                <li class="p-2 bg-gray-700 rounded-md flex justify-between items-center">
                    <span>{{ $team->nombre }} (Ptos: {{ $team->puntos }})</span>

                    {{-- ACCIONES DE EDITAR/ELIMINAR --}}
                    <div class="flex space-x-2">
                        {{-- ⬇️ CORRECCIÓN: Usar enlace <a> para la ruta de edición ⬇️ --}}
                        <a href="{{ route('teams.edit', $team->id) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded-md text-xs">Editar</a>

                        {{-- Formulario de Eliminación --}}
                        <form method="POST" action="{{ route('teams.destroy', $team->id) }}"
                            onsubmit="return confirm('¿Seguro que quieres eliminar a {{ $team->nombre }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-md text-xs">Eliminar</button>
                        </form>
                    </div>
                </li>
            @empty
                <li class="p-2 text-center text-gray-500">No hay equipos registrados aún.</li>
            @endforelse
        </ul>
    </div>
</div>
