@extends('index')

@section('content')
    <div class="max-w-7xl mx-auto py-8 w-full">

        {{-- ---------------------------------------------------- --}}
        {{-- CABECERA Y MÉTRICAS PRINCIPALES --}}
        {{-- ---------------------------------------------------- --}}
        <div class="card p-6 shadow-2xl mb-8 border-b-4 border-primary">
            <div class="flex flex-col items-center space-y-4">

                {{-- Foto y Nombre --}}
                <img src="{{ $jugador->foto_url ?? asset('images/placeholder_jug.png') }}" alt="{{ $jugador->nombre }}"
                    class="w-40 h-40 rounded-full object-cover border-4 border-secondary shadow-lg mb-3" />

                <div class="flex items-center justify-center">
                    <h1 class="text-4xl font-extrabold text-white">{{ $jugador->nombre }}</h1>

                    {{-- ✅ Condicional para mostrar el icono de lesión --}}
                    @if ($jugador->esta_lesionado)
                        <span title="Lesionado" class="ml-3 text-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        </span>
                    @endif
                </div>
                <p class="text-xl text-gray-400">
                    #{{ $jugador->numero }} &bull; {{ ucfirst($jugador->posicion_especifica) }}
                </p>
                <p class="text-lg text-primary">
                    Equipo: <a href="{{ route('team.profile', $equipo->id) }}"
                        class="font-bold hover:underline">{{ $equipo->nombre ?? 'Sin equipo' }}</a>
                </p>

                {{-- Bloque de Métricas Rápidas (Se mantiene) --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 w-full md:w-3/4 text-center">
                    <div class="bg-gray-700/50 p-3 rounded-lg">
                        <span class="text-xl font-bold block text-red-400">{{ $jugador->goles }}</span>
                        <span class="text-xs text-gray-400">Goles</span>
                    </div>
                    <div class="bg-gray-700/50 p-3 rounded-lg">
                        <span class="text-xl font-bold block text-yellow-400">{{ $jugador->asistencias }}</span>
                        <span class="text-xs text-gray-400">Asist.</span>
                    </div>
                    <div class="bg-gray-700/50 p-3 rounded-lg">
                        <span class="text-xl font-bold block text-blue-400">{{ $jugador->paradas }}</span>
                        <span class="text-xs text-gray-400">Paradas</span>
                    </div>
                    <div class="bg-gray-700/50 p-3 rounded-lg">
                        <span
                            class="text-xl font-bold block text-purple-400">{{ $jugador->goles + $jugador->asistencias }}</span>
                        <span class="text-xs text-gray-400">Impacto G/A</span>
                    </div>
                </div>
            </div>
        </div> {{-- Fin Cabecera --}}


        {{-- ---------------------------------------------------- --}}
        {{-- SECCIÓN ANALÍTICA Y EVENTOS --}}
        {{-- ---------------------------------------------------- --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Columna 1: Rendimiento y Disciplina (Densificada) --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Bloque de Rendimiento Avanzado (ACTUALIZADO CON TASA DE VICTORIA Y PJ) --}}
                <div class="card p-4 shadow-xl">
                    <h4 class="text-xl font-bold mb-3 text-primary">Rendimiento y Frecuencia</h4>
                    <div class="space-y-3">
                        <p class="text-gray-300 flex justify-between">
                            Partidos Jugados (Equipo): <span class="font-bold text-gray-300">{{ $pj }}</span>
                        </p>
                        <p class="text-gray-300 flex justify-between">
                            Tasa de Victoria (Equipo): <span
                                class="font-bold text-green-400">{{ $winRateWithPlayer }}%</span>
                        </p>
                        <p class="text-gray-300 flex justify-between border-t border-gray-700 pt-3 mt-3">
                            Goles Promedio/PJ: <span class="font-bold text-red-400">{{ $gpjRatio }}</span>
                        </p>
                        <p class="text-gray-300 flex justify-between">
                            Asist. Promedio/PJ: <span class="font-bold text-yellow-400">{{ $apjRatio }}</span>
                        </p>
                        <p class="text-gray-300 flex justify-between">
                            Contribución Promedio/PJ: <span
                                class="font-bold text-purple-400">{{ $contributionPerMatch }}</span>
                        </p>
                    </div>
                </div>

                <div class="card p-4 shadow-xl">
                    <h4 class="text-xl font-bold mb-3 text-secondary">Récords de Anotación</h4>
                    <div class="space-y-3 text-sm">

                        <p class="text-gray-300 flex justify-between">
                            Dobletes (2 Goles): <span
                                class="font-bold text-yellow-300">{{ $goalRecords['dobletes'] }}</span>
                        </p>
                        <p class="text-gray-300 flex justify-between">
                            Hat-Tricks (3 Goles): <span
                                class="font-bold text-red-400">{{ $goalRecords['hat_tricks'] }}</span>
                        </p>
                        <p class="text-gray-300 flex justify-between">
                            Póker (4 Goles): <span class="font-bold text-purple-400">{{ $goalRecords['poker'] }}</span>
                        </p>
                        <p class="text-gray-300 flex justify-between">
                            Manita (5 Goles): <span class="font-bold text-primary">{{ $goalRecords['manita'] }}</span>
                        </p>
                        <p class="text-gray-300 flex justify-between border-t border-gray-700 pt-3 mt-3">
                            Más de 5 Goles: <span class="font-bold text-white">{{ $goalRecords['mas_cinco'] }}</span>
                        </p>
                    </div>
                    {{-- ✅ NUEVA SECCIÓN: Desglose de Goles por Tipo --}}
                    @if ($goalsByType->isNotEmpty())
                        <div class="border-t border-gray-700 pt-4 mt-4">
                            <h5 class="text-sm font-semibold text-gray-400 mb-2">Desglose de Goles</h5>
                            <div class="space-y-3 text-sm">
                                @foreach ($goalsByType as $type => $count)
                                    {{-- Usamos un valor por defecto si el tipo es nulo o 'Jugada' --}}
                                    @php
                                        $goalTypeName = match ($type) {
                                            'cabeza' => 'De Cabeza',
                                            'penalti' => 'De Penalti',
                                            'tiro libre' => 'De Tiro Libre',
                                            'en contra' => 'En Contra',
                                            'de jugada' => 'De Jugada Personal',
                                            default => 'De Jugada Personal',
                                        };
                                    @endphp
                                    <p class="text-gray-300 flex justify-between">
                                        {{ $goalTypeName }}:
                                        <span class="font-bold text-primary">{{ $count }}</span>
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ⬇️ NUEVO BLOQUE: EFICIENCIA Y DISCIPLINA ⬇️ --}}
                <div class="card p-4 shadow-xl">
                    <h4 class="text-xl font-bold mb-3 text-secondary">Eficiencia y Disciplina</h4>
                    <div class="space-y-3">
                        <p class="text-gray-300 flex justify-between">
                            Participación en Goles: <span class="font-bold text-primary">{{ $participationRate }}%</span>
                        </p>

                        @if (strtolower($jugador->posicion_especifica) === 'portero')
                            <p class="text-gray-300 flex justify-between">
                                Eficiencia Portero: <span class="font-bold text-blue-400">{{ $keeperEfficiency }}%</span>
                            </p>
                        @endif

                        <p class="text-gray-300 flex justify-between border-t border-gray-700 pt-3 mt-3">
                            Total Tarjetas Amarillas: <span
                                class="font-bold text-yellow-300">{{ $jugador->amarillas }}</span>
                        </p>
                        <p class="text-gray-300 flex justify-between">
                            Total Tarjetas Rojas: <span class="font-bold text-red-500">{{ $jugador->rojas }}</span>
                        </p>
                        <p class="text-gray-300 flex justify-between">
                            Puntuación Disciplinaria: <span class="font-bold text-red-400">{{ $disciplineScore }}
                                Pts</span>
                        </p>
                        {{-- Evento	Multiplicador (Peso)	Razón
                        Goles	+3	La contribución más alta al resultado.
                        Asistencias	+2	Contribución ofensiva directa.
                        Paradas	+0.5	Contribución defensiva (impacto moderado).
                        Tarjeta Roja	-3	Máxima penalización disciplinaria (deja al equipo con 10).
                        Tarjeta Amarilla	-1	Penalización disciplinaria leve. --}}
                        <p class="text-gray-300 flex justify-between border-t border-gray-700 pt-3 mt-3">
                            <span class="flex items-center space-x-1">
                                Puntuación MVP Total
                                <div class="tooltip-container">
                                    {{-- Icono de Interrogación --}}
                                    <span class="material-symbols-outlined text-gray-500 text-sm">info</span>

                                    {{-- Contenido del Tooltip --}}
                                    <div class="tooltip-content">
                                        <strong class="text-white text-base block mb-1">Cálculo de Puntuación MVP:</strong>
                                        <p class="text-xs">
                                            (Goles × 3) + (Asistencias × 2) + (Paradas × 0.5) <br>
                                            - (Rojas × 3) - (Amarillas × 1)
                                        </p>
                                        <hr class="border-gray-600 my-1">
                                        <div class="flex justify-between font-bold text-xs">
                                            <span class="text-green-400">Goles/Asist. (+):</span>
                                            <span class="text-red-400">Tarjetas (-):</span>
                                        </div>
                                    </div>
                                </div>
                            </span>

                            <span class="font-bold text-purple-400">{{ number_format($mvpScore, 1) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Columna 2: LISTA DE EVENTOS RECIENTES (Historial de Partidos) --}}
            <div class="lg:col-span-2 card p-4 shadow-xl">
                <h3 class="text-2xl font-bold mb-4 text-primary">Historial de Eventos Recientes</h3>
                <p class="text-sm text-gray-500 mb-4">Muestra los últimos 10 eventos registrados por el jugador.</p>

                <div class="space-y-3">
                    @forelse($recentEvents as $event)
                        @php
                            $p = $event->partido;

                            // ⬇️ DEFINICIÓN DE CLASES Y TEXTO DEL EVENTO ⬇️
                            $eventText = match ($event->tipo_evento) {
                                'gol' => match ($event->goal_type) {
                                    'jugada' => 'Gol de Jugada Individual ⚽',
                                    'cabeza' => 'Marcó un Gol de Cabeza ⚽',
                                    'penalti' => 'Marcó de Penalti ⚽',
                                    'tiro libre' => 'Marcó de Tiro Libre ⚽',
                                    'en contra' => 'Marcó un Gol en Contra ⚽',
                                    default => 'Marcó un Gol ⚽',
                                },
                                'asistencia' => 'Dio una Asistencia 👟',
                                'parada' => 'Realizó una Parada 🧤',
                                'amarilla' => 'Recibió Tarjeta Amarilla 🟨',
                                'roja' => 'Recibió Tarjeta Roja 🟥',
                                default => ucfirst($event->tipo_evento),
                            };

                            // ⬇️ ASIGNACIÓN DEL COLOR DE FONDO POR TIPO DE EVENTO ⬇️
                            $eventBgColor = match ($event->tipo_evento) {
                                'gol' => 'bg-green-800/50 border-green-400', // Verde para Goles
                                'asistencia' => 'bg-yellow-800/40 border-yellow-400', // Amarillo para Asistencia
                                'parada' => 'bg-blue-800/40 border-blue-400', // Azul para Paradas
                                'amarilla' => 'bg-orange-900/40 border-yellow-500', // Naranja/Amarillo para Amarilla
                                'roja' => 'bg-red-900/40 border-red-500', // Rojo Oscuro para Roja
                                default => 'bg-gray-800/60 border-gray-600',
                            };

                            $score = "{$p->goles_local} - {$p->goles_visitante}";
                            $opponent =
                                $p->equipo_local_id === $jugador->equipo_id
                                    ? $p->visitorTeam->nombre
                                    : $p->localTeam->nombre;
                        @endphp

                        <div class="flex justify-between items-center p-3 rounded-lg border-l-4 {{ $eventBgColor }}">
                            <div class="flex-grow">
                                <span class="font-semibold text-white block">{{ $eventText }}
                                    ({{ $event->minuto }}')
                                </span>
                                <span class="text-xs text-gray-400">vs {{ $opponent }}
                                    ({{ $score }})</span><br>
                                <span class="text-xs text-gray-400">Jornada {{ $p->jornada }}</span>
                            </div>
                            <span
                                class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($p->fecha_hora)->format('d M') }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 p-3">Este jugador aún no tiene eventos registrados.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection
