@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-900 text-white py-10">
        <div class="container mx-auto px-4">
            <h1
                class="text-4xl font-bold mb-2 text-center text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-500">
                11 Ideal de la Liga
            </h1>
            <p class="text-center text-gray-400 mb-10">Los mejores jugadores basados en estadísticas y rendimiento actual
            </p>

            <!-- Football Pitch Container -->
            <div
                class="relative w-full max-w-4xl mx-auto aspect-[2/3] md:aspect-[4/3] bg-green-700 rounded-xl border-4 border-white/20 shadow-2xl overflow-hidden">

                <!-- Pitch Markings -->
                <div class="absolute inset-0 opacity-30 pointer-events-none">
                    <!-- Grass Pattern -->
                    <div class="w-full h-full"
                        style="background-image: repeating-linear-gradient(0deg, transparent, transparent 50px, rgba(0,0,0,0.1) 50px, rgba(0,0,0,0.1) 100px);">
                    </div>

                    <!-- Center Line -->
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-white transform -translate-y-1/2"></div>

                    <!-- Center Circle -->
                    <div
                        class="absolute top-1/2 left-1/2 w-24 h-24 md:w-40 md:h-40 border-2 border-white rounded-full transform -translate-x-1/2 -translate-y-1/2">
                    </div>

                    <!-- Penalty Areas -->
                    <div
                        class="absolute top-0 left-1/2 w-48 h-24 border-2 border-t-0 border-white transform -translate-x-1/2">
                    </div>
                    <div
                        class="absolute bottom-0 left-1/2 w-48 h-24 border-2 border-b-0 border-white transform -translate-x-1/2">
                    </div>
                </div>

                <!-- Formation Grid (4-3-3) -->
                <div class="absolute inset-0 flex flex-col justify-between py-2 md:py-4 px-2 md:px-4">

                    <!-- Forwards (Top) - Spread out -->
                    <div class="flex justify-between items-start h-1/4 px-4 md:px-20 pt-8 md:pt-12">
                        @foreach($bestEleven['forwards'] as $player)
                            <x-ideal-player-card :player="$player" position="Delantero" />
                        @endforeach
                    </div>

                    <!-- Midfielders - Centered/Lower -->
                    <div class="flex justify-around items-center h-1/4 px-2 md:px-12 pt-8 md:pt-12">
                        @foreach($bestEleven['midfielders'] as $player)
                            <x-ideal-player-card :player="$player" position="Medio" />
                        @endforeach
                    </div>

                    <!-- Defenders - Lower -->
                    <div class="flex justify-around items-end h-1/4 px-1 md:px-8 pb-2 md:pb-4">
                        @foreach($bestEleven['defenders'] as $player)
                            <x-ideal-player-card :player="$player" position="Defensa" />
                        @endforeach
                    </div>

                    <!-- Goalkeeper (Bottom) - Bottom edge -->
                    <div class="flex justify-center items-end h-1/4 pb-1 md:pb-2">
                        @if($bestEleven['goalkeeper'])
                            <x-ideal-player-card :player="$bestEleven['goalkeeper']" position="Portero" />
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection