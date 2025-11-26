@extends('layouts.app')

@section('content')
    <div class="min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1
                    class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-500 mb-4">
                    Zona de Minijuegos
                </h1>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    Demuestra tus habilidades, compite con otros usuarios y alcanza la cima de la clasificación.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- KEEPY UPPY CARD -->
                <div
                    class="group relative bg-gray-800 rounded-2xl overflow-hidden shadow-2xl transition-all duration-300 hover:-translate-y-2 hover:shadow-green-500/20 border border-gray-700 hover:border-green-500/50">
                    <!-- Image -->
                    <div class="h-48 overflow-hidden relative">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent z-10"></div>
                        <img src="{{ asset('images/games/keepy-uppy.png') }}" alt="Keepy Uppy"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <div
                            class="absolute top-4 right-4 z-20 bg-green-500 text-black text-xs font-bold px-2 py-1 rounded-full">
                            POPULAR
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 relative z-20">
                        <h3 class="text-2xl font-bold text-white mb-2 group-hover:text-green-400 transition-colors">
                            Keepy Uppy Challenge
                        </h3>
                        <p class="text-gray-400 mb-6 text-sm line-clamp-2">
                            ¡Mantén el balón en el aire! Desafía la gravedad, enfréntate al viento y demuestra tu control.
                        </p>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <span class="material-symbols-outlined text-yellow-500 text-lg">trophy</span>
                                <span>Top: {{ $keepyUppyTopScore }}</span>
                            </div>

                            <a href="{{ route('game.keepy-uppy') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold rounded-lg hover:from-green-500 hover:to-emerald-500 transition-all shadow-lg hover:shadow-green-500/30">
                                <span class="material-symbols-outlined mr-2">play_circle</span>
                                Jugar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- PENALTY SHOOTOUT CARD -->
                <div
                    class="group relative bg-gray-800 rounded-2xl overflow-hidden shadow-2xl transition-all duration-300 hover:-translate-y-2 hover:shadow-blue-500/20 border border-gray-700 hover:border-blue-500/50">
                    <!-- Image -->
                    <div class="h-48 overflow-hidden relative">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent z-10"></div>
                        <img src="{{ asset('images/games/penalty-shootout.png') }}" alt="Penalty Shootout"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <div
                            class="absolute top-4 right-4 z-20 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                            NUEVO
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 relative z-20">
                        <h3 class="text-2xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                            Penalty Shootout
                        </h3>
                        <p class="text-gray-400 mb-6 text-sm line-clamp-2">
                            ¡Define el partido! Apunta, ajusta la potencia y vence al portero en esta tanda de penaltis.
                        </p>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <span class="material-symbols-outlined text-yellow-500 text-lg">trophy</span>
                                <span>Top: {{ $penaltyTopScore }}</span>
                            </div>

                            <a href="{{ route('game.penalty') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-lg hover:from-blue-500 hover:to-indigo-500 transition-all shadow-lg hover:shadow-blue-500/30">
                                <span class="material-symbols-outlined mr-2">play_circle</span>
                                Jugar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- COMING SOON CARD -->
                <!-- PORTERO RUNNER CARD -->
                <div
                    class="group relative bg-gray-800 rounded-2xl overflow-hidden shadow-2xl transition-all duration-300 hover:-translate-y-2 hover:shadow-purple-500/20 border border-gray-700 hover:border-purple-500/50">
                    <!-- Image -->
                    <div class="h-48 overflow-hidden relative">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent z-10"></div>
                        <img src="{{ asset('images/games/portero-runner.png') }}" alt="Portero Runner"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <div
                            class="absolute top-4 right-4 z-20 bg-purple-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                            NUEVO
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 relative z-20">
                        <h3 class="text-2xl font-bold text-white mb-2 group-hover:text-purple-400 transition-colors">
                            Portero Runner
                        </h3>
                        <p class="text-gray-400 mb-6 text-sm line-clamp-2">
                            ¡Esquiva bombas y atrapa balones! Muévete entre carriles y usa power-ups para sobrevivir.
                        </p>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <span class="material-symbols-outlined text-yellow-500 text-lg">trophy</span>
                                <span>Top: {{ $porteroRunnerTopScore }}</span>
                            </div>

                            <a href="{{ route('game.portero-runner') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-lg hover:from-purple-500 hover:to-pink-500 transition-all shadow-lg hover:shadow-purple-500/30">
                                <span class="material-symbols-outlined mr-2">play_circle</span>
                                Jugar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection