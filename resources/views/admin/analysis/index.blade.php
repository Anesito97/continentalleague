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

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-primary to-emerald-600 hover:from-primary/90 hover:to-emerald-600/90 text-white font-bold py-4 rounded-lg shadow-lg hover:shadow-primary/50 transition transform hover:scale-[1.02] flex items-center justify-center gap-2 mt-4">
                        <span class="material-symbols-outlined">analytics</span>
                        Generar Informe Táctico
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection