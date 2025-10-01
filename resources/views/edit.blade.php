<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar {{ ucfirst($type) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilos base de Tailwind, etc. */
        .card {
            background-color: #1f2937;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }
        input[type="text"], input[type="number"], input[type="file"], input[type="date"], input[type="time"], select {
            background-color: #374151; /* Color de fondo para inputs */
            border-color: #4b5563; /* Color de borde */
        }
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100">
    <div class="max-w-4xl mx-auto py-10">
        <div class="card p-6">
            <h2 class="text-4xl font-bold text-white mb-6 border-b border-blue-500 pb-2">
                Editar {{ ucfirst($type) }}: {{ $item->nombre ?? 'ID ' . $item->id }}
            </h2>

            @include('partials.alerts') {{-- Asumiendo que tienes un archivo alerts.blade.php para mensajes de sesión --}}

            {{-- 
                FORMULARIO DE ACTUALIZACIÓN (PUT)
                
                CORRECCIÓN CLAVE: Se usa una expresión ternaria para asegurar que 'match'
                se pluralice correctamente como 'matches' y no 'matchs' al llamar a la ruta.
                
                Ruta para actualizar: 
                - Si es 'match': route('matches.update', ...)
                - Si es 'team' o 'player': route('teams.update', ...) o route('players.update', ...)
            --}}
            <form method="POST" 
                  action="{{ route($type === 'match' ? 'matches.update' : $type . 's.update', $item->id) }}" 
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                {{-- FORMULARIO DE EQUIPO --}}
                @if($type === 'team')
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $item->nombre) }}" required class="w-full px-3 py-2 mb-4 bg-gray-700 rounded-md text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Logo Actual</label>
                        <img src="{{ $item->escudo_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=LOGO' }}" class="w-16 h-16 rounded-full object-cover my-2">
                        <label class="block text-sm font-medium text-gray-400">Subir Nuevo Logo</label>
                        <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-300 mt-1 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                {{-- FORMULARIO DE JUGADOR --}}
                @elseif($type === 'player')
                    {{-- 
                        ASUME que este es el contenido del include, ya que necesitamos los campos.
                        Si tienes un archivo real, asegúrate de que exista en 'admin/forms/player_edit.blade.php' 
                    --}}
                    @include('admin.forms.player_edit', ['player' => $item, 'teams' => $teams])

                {{-- FORMULARIO DE PARTIDO --}}
                @elseif($type === 'match')
                    {{-- 
                        ASUME que este es el contenido del include, ya que necesitamos los campos.
                        Si tienes un archivo real, asegúrate de que exista en 'admin/forms/match_edit.blade.php' 
                    --}}
                    @include('admin.forms.match_edit', ['match' => $item, 'teams' => $teams])
                @endif

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">Guardar Cambios</button>
                    <a href="{{ route($type === 'match' ? 'admin.matches' : 'admin.' . $type . 's') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">Cancelar</a>
                </div>
            </form>

            {{-- 
                FORMULARIO DE ELIMINACIÓN (DELETE)
                
                CORRECCIÓN CLAVE: También se aplica la expresión ternaria aquí para la ruta destroy.
            --}}
            <form method="POST" 
                  action="{{ route($type === 'match' ? 'matches.destroy' : $type . 's.destroy', $item->id) }}" 
                  onsubmit="return confirm('¿CONFIRMAS ELIMINAR {{ ucfirst($type) }} ({{ $item->nombre ?? $item->id }})?');" 
                  class="mt-4 border-t border-gray-700 pt-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition">Eliminar {{ ucfirst($type) }}</button>
            </form>
        </div>
    </div>
</body>
</html>