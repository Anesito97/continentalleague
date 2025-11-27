@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 drop-shadow-lg mb-4">
                MVP por Jornada
            </h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Los jugadores más destacados de cada jornada de la Continental League.
            </p>
        </div>

        <!-- Grid -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($mvpData as $data)
                <div class="relative bg-gray-800/50 rounded-2xl p-6 border border-white/5 flex flex-col items-center justify-between min-h-[300px] transition hover:bg-gray-800/80">
                    
                    <!-- Jornada Badge -->
                    <div class="absolute top-4 left-4 bg-white/10 px-3 py-1 rounded-full border border-white/10">
                        <span class="text-xs font-bold text-gray-300 uppercase tracking-wider">Jornada {{ $data['jornada'] }}</span>
                    </div>

                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        @if($data['is_finished'])
                            <span class="flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-700 text-gray-300">
                                {{ $data['status_label'] }}
                            </span>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 flex flex-col items-center justify-center w-full mt-8">
                        @if($data['is_finished'] && $data['mvp'])
                            <!-- MVP Card -->
                            <div class="transform scale-110">
                                <x-ideal-player-card :player="$data['mvp']" :position="$data['mvp']->posicion_general" />
                            </div>
                            
                            <!-- Stats Summary (Optional) -->
                            <div class="mt-6 text-center">
                                <div class="text-2xl font-black text-yellow-400">{{ number_format($data['mvp']->rating, 0) }} pts</div>
                                <div class="text-xs text-gray-500 uppercase tracking-widest">Puntuación</div>
                                
                                @if(isset($data['mvp']->mvp_reason))
                                    <div class="mt-3 px-3 py-1 rounded-full bg-yellow-500/10 border border-yellow-500/20 inline-block">
                                        <span class="text-xs font-bold text-yellow-500 uppercase tracking-wider">{{ $data['mvp']->mvp_reason }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <!-- Locked/Pending State -->
                            <div class="flex flex-col items-center justify-center opacity-30">
                                <div class="w-24 h-36 rounded-lg border-2 border-dashed border-gray-500 flex items-center justify-center bg-gray-800">
                                    <span class="text-4xl font-bold text-gray-600">?</span>
                                </div>
                                <p class="mt-4 text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $data['is_finished'] ? 'Sin datos suficientes' : 'Pendiente' }}
                                </p>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    </div>
@endsection
