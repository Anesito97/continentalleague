@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-white mb-2 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-4xl">psychology</span>
                Análisis Profundo
            </h1>
            <p class="text-gray-400 mb-8">Selecciona tu equipo y el rival para generar un informe táctico avanzado.</p>

            <div class="bg-card-bg border border-white/10 rounded-xl p-8 shadow-2xl relative overflow-hidden">
                {{-- Fondo decorativo --}}
                <div
                    class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary/10 blur-[80px] rounded-full pointer-events-none">
                </div>

                <form action="{{ route('admin.analysis.analyze') }}" method="POST" class="space-y-6 relative z-10">
                    @csrf

                    {{-- Selección de MI EQUIPO --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Tu Equipo</label>
                        <div class="relative">
                            <select name="my_team_id"
                                class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-4 appearance-none focus:border-primary focus:ring-1 focus:ring-primary transition cursor-pointer">
                                <option value="" disabled selected>Selecciona tu equipo...</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->nombre }}</option>
                                @endforeach
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                                <span class="material-symbols-outlined">expand_more</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <span class="bg-gray-800 text-gray-400 rounded-full p-2 border border-white/10">
                            <span class="material-symbols-outlined">swords</span>
                        </span>
                    </div>

                    {{-- Selección del RIVAL --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">El Rival</label>
                        <div class="relative">
                            <select name="opponent_id"
                                class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-4 appearance-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition cursor-pointer">
                                <option value="" disabled selected>Selecciona al rival...</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->nombre }}</option>
                                @endforeach
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                                <span class="material-symbols-outlined">expand_more</span>
                            </div>
                        </div>
                    </div>

                    {{-- SELECCIÓN DE JUGADORES (NUEVO) --}}
                    <div id="players-section" class="hidden mt-8">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-users-gear text-primary"></i>
                            Disponibilidad de Plantilla
                        </h3>
                        <div class="bg-card-bg/50 border border-white/10 rounded-xl p-4">
                            <p class="text-sm text-gray-400 mb-4">Desmarca los jugadores que no estarán disponibles para
                                este partido.</p>
                            <div id="players-grid"
                                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-60 overflow-y-auto custom-scrollbar">
                                {{-- Players will be injected here via JS --}}
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit"
                            class="bg-gradient-to-r from-primary to-emerald-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-primary/50 transition-all transform hover:scale-105 flex items-center gap-2">
                            <i class="fa-solid fa-microchip"></i>
                            Ejecutar Análisis Profundo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('my_team_id').addEventListener('change', function () {
            const teamId = this.value;
            const playersSection = document.getElementById('players-section');
            const playersGrid = document.getElementById('players-grid');

            if (teamId) {
                // Fetch players (Assuming we have an API endpoint or using a raw route)
                // For now, we'll use a simple fetch to a new route we need to create or reuse an existing one.
                // Let's assume we create a simple route /api/teams/{id}/players

                fetch(`/api/teams/${teamId}/players`)
                    .then(response => response.json())
                    .then(data => {
                        playersGrid.innerHTML = '';
                        data.forEach(player => {
                            const div = document.createElement('div');
                            div.className = 'flex items-center gap-2 bg-white/5 p-2 rounded hover:bg-white/10 transition cursor-pointer';
                            div.innerHTML = `
                                <input type="checkbox" name="available_players[]" value="${player.id}" checked class="form-checkbox h-4 w-4 text-primary rounded border-gray-600 bg-gray-700 focus:ring-primary">
                                <span class="text-sm text-gray-300 truncate">${player.nombre}</span>
                            `;
                            // Toggle checkbox on div click
                            div.addEventListener('click', (e) => {
                                if (e.target.type !== 'checkbox') {
                                    const checkbox = div.querySelector('input');
                                    checkbox.checked = !checkbox.checked;
                                }
                            });
                            playersGrid.appendChild(div);
                        });
                        playersSection.classList.remove('hidden');
                    })
                    .catch(error => console.error('Error fetching players:', error));
            } else {
                playersSection.classList.add('hidden');
            }
        });
    </script>
@endsection