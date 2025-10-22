@extends('index') {{-- Hereda el layout principal para mantener el diseño --}}

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <h2 class="text-4xl font-bold text-white mb-6 border-b border-green-700 pb-2">
            Jugadores de: {{ $equipoActual->nombre }}
        </h2>

        <a href="{{ route('admin.teams') }}" class="text-sm text-green-400 hover:text-green-500 mb-4 inline-block">
            &larr; Volver a Gestión de Equipos
        </a>

        <div class="overflow-x-auto card p-4">
            <h4 class="text-xl font-semibold mb-3">Listado de Jugadores ({{ $players->count() }})</h4>

            <table class="min-w-full divide-y divide-gray-700">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400 bg-gray-800">
                        <th class="py-3 px-2">#</th>
                        <th class="py-3 px-2">Jugador</th>
                        <th class="py-3 px-2 text-center">Pos</th>
                        <th class="py-3 px-2 text-center">⚽</th>
                        <th class="py-3 px-2 text-center">👟</th>
                        <th class="py-3 px-2 text-center hidden sm:table-cell">🧤</th>
                        <th class="py-3 px-2 text-center hidden sm:table-cell">🟨</th>
                        <th class="py-3 px-2 text-center hidden sm:table-cell">🟥</th>
                        <th class="py-3 px-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-sm">
                    @forelse($players as $player)
                        <tr class="hover:bg-gray-700 transition">
                            <td class="py-3 px-2 font-bold">{{ $player->numero }}</td>
                            <td class="py-3 px-2 flex items-center">
                                <a href="{{ route('player.profile', $player->id) }}"
                                    class="flex items-center hover:text-green-300 transition">
                                    <img src="{{ $player->foto_url ?? 'https://placehold.co/50x50/1f2937/FFFFFF?text=JUG' }}"
                                        onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=JUG'"
                                        class="w-8 h-8 rounded-full object-cover mr-3" />
                                    <span class="font-medium text-white">{{ $player->nombre }}</span>
                                </a>
                            </td>
                            <td class="py-3 px-2 text-center text-gray-300">{{ ucfirst($player->posicion_especifica) }}</td>
                            <td class="py-3 px-2 text-center text-red-400 font-bold">{{ $player->goles }}</td>
                            <td class="py-3 px-2 text-center text-yellow-400 font-bold">{{ $player->asistencias }}</td>
                            <td class="py-3 px-2 text-center hidden sm:table-cell">{{ $player->paradas }}</td>
                            <td class="py-3 px-2 text-center hidden sm:table-cell text-yellow-300">{{ $player->amarillas }}
                            </td>
                            <td class="py-3 px-2 text-center hidden sm:table-cell text-red-500">{{ $player->rojas }}</td>
                            <td class="py-3 px-2 text-center">
                                <div class="flex space-x-1 justify-center">
                                    <a href="{{ route('players.edit', $player->id) }}"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded-md text-xs">Editar</a>

                                    <form method="POST" action="{{ route('players.destroy', $player->id) }}"
                                        onsubmit="return confirm('¿Eliminar a {{ $player->nombre }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-md text-xs">X</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-4 text-center text-gray-500">No hay jugadores registrados en este
                                equipo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
