@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-900 text-white py-10">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-bold mb-2 text-center text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-500">
                {{ $team ? 'Posible Alineación - ' . $team->nombre : '11 Ideal de la Liga' }}
            </h1>
            <p class="text-center text-gray-400 mb-6 text-sm md:text-base">
                {{ $team ? 'Los mejores jugadores disponibles para este equipo' : 'Los mejores jugadores basados en estadísticas y rendimiento actual' }}
            </p>

            @if($team)
                <div class="flex justify-center mb-6">
                    <a href="{{ route('ideal-eleven') }}"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-full text-sm font-bold transition border border-gray-600 shadow-md">
                        Ver 11 Ideal de la Liga
                    </a>
                </div>
            @endif

            <div class="relative w-full max-w-4xl mx-auto aspect-[2/3] md:aspect-[4/3] bg-green-700 rounded-xl border-4 border-white/20 shadow-2xl overflow-hidden select-none">

                <div class="absolute inset-0 opacity-30 pointer-events-none">
                    <div class="w-full h-full" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 50px, rgba(0,0,0,0.1) 50px, rgba(0,0,0,0.1) 100px);"></div>
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-white transform -translate-y-1/2"></div>
                    <div class="absolute top-1/2 left-1/2 w-24 h-24 md:w-40 md:h-40 border-2 border-white rounded-full transform -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute top-0 left-1/2 w-48 h-24 border-2 border-t-0 border-white transform -translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-1/2 w-48 h-24 border-2 border-b-0 border-white transform -translate-x-1/2"></div>
                </div>

                <div class="absolute inset-0 flex flex-col justify-between py-4 md:py-8 z-10">

                    <div class="w-full flex items-start justify-evenly px-8 md:px-24 z-0 pt-2">
                        @foreach($bestEleven['forwards'] as $player)
                            <x-ideal-player-card :player="$player" position="Delantero" />
                        @endforeach
                    </div>

                    <div class="w-full flex items-center justify-around px-2 md:px-12 z-10">
                        @foreach($bestEleven['midfielders'] as $player)
                            <div class="mt-4 md:mt-0"> 
                                <x-ideal-player-card :player="$player" position="Medio" />
                            </div>
                        @endforeach
                    </div>

                    <div class="w-full flex items-end justify-between px-4 md:px-16 z-20 pb-2">
                        @foreach($bestEleven['defenders'] as $player)
                            <x-ideal-player-card :player="$player" position="Defensa" />
                        @endforeach
                    </div>

                    <div class="w-full flex items-end justify-center z-30 pb-1">
                        @if($bestEleven['goalkeeper'])
                            <x-ideal-player-card :player="$bestEleven['goalkeeper']" position="Portero" />
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection