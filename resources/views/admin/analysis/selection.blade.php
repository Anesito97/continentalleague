@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-white mb-2 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-4xl">groups</span>
                Selección de Plantilla
            </h1>
            <p class="text-gray-400 mb-8">Confirma los jugadores disponibles de <strong>{{ $myTeam->nombre }}</strong> para
                el partido contra <strong>{{ $opponent->nombre }}</strong>.</p>

            <div class="bg-card-bg border border-white/10 rounded-xl p-8 shadow-2xl relative overflow-hidden">
                {{-- Fondo decorativo --}}
                <div
                    class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary/10 blur-[80px] rounded-full pointer-events-none">
                </div>

                <form action="{{ route('admin.analysis.analyze') }}" method="POST" class="relative z-10">
                    @csrf
                    <input type="hidden" name="my_team_id" value="{{ $myTeam->id }}">
                    <input type="hidden" name="opponent_id" value="{{ $opponent->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                        @foreach($myTeam->jugadores as $player)
                            <div class="bg-gray-900/50 border border-white/5 rounded-lg p-3 flex items-center gap-3 hover:bg-white/5 transition cursor-pointer select-none"
                                onclick="toggleCheckbox('player_{{ $player->id }}')">
                                <div class="relative">
                                    <input type="checkbox" id="player_{{ $player->id }}" name="available_players[]"
                                        value="{{ $player->id }}" checked
                                        class="w-5 h-5 text-primary bg-gray-700 border-gray-600 rounded focus:ring-primary focus:ring-2 cursor-pointer"
                                        onclick="event.stopPropagation()">
                                </div>
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-800 overflow-hidden flex-shrink-0 border border-white/10">
                                        <img src="{{ $player->foto_url ?? 'https://placehold.co/40x40' }}"
                                            alt="{{ $player->nombre }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex flex-col truncate">
                                        <span class="text-sm font-bold text-white truncate">{{ $player->nombre }}</span>
                                        <span class="text-xs text-gray-400 uppercase">{{ $player->posicion_general }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between items-center pt-6 border-t border-white/10">
                        <a href="{{ route('admin.analysis.index') }}"
                            class="text-gray-400 hover:text-white transition flex items-center gap-2">
                            <i class="fa-solid fa-arrow-left"></i>
                            Volver
                        </a>
                        <button type="submit"
                            class="bg-gradient-to-r from-primary to-emerald-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-primary/50 transition-all transform hover:scale-105 flex items-center gap-2">
                            <i class="fa-solid fa-microchip"></i>
                            Generar Informe Final
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleCheckbox(id) {
            const checkbox = document.getElementById(id);
            checkbox.checked = !checkbox.checked;
        }
    </script>
@endsection