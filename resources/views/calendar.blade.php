@extends('index')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <h2 class="text-3xl font-bold text-white mb-6 border-b border-primary pb-2 flex items-center">
            <span class="material-symbols-outlined mr-3 text-3xl text-primary">calendar_month</span>
            Calendario de Partidos
        </h2>

        {{-- ⬇️ 1. NAVEGACIÓN DE PESTAÑAS (TABS) ⬇️ --}}
        <div class="flex space-x-3 mb-6 border-b border-gray-700 pb-1 overflow-x-auto">

            @php
                // Función auxiliar para generar el enlace con el filtro
                $getCalendarRoute = fn($status) => route('matches.calendar', ['status' => $status]);
                $tabClasses = 'px-4 py-2 text-sm font-semibold border-b-2 transition duration-300 ';
            @endphp

            {{-- PESTAÑA 1: PENDIENTES (Por defecto) --}}
            <a href="{{ $getCalendarRoute('pending') }}"
                class="{{ $tabClasses }} {{ $activeFilter === 'pending' ? 'border-primary text-primary' : 'border-transparent text-gray-400 hover:border-gray-500' }}">
                Partidos Pendientes
            </a>

            {{-- PESTAÑA 2: FINALIZADOS --}}
            <a href="{{ $getCalendarRoute('finished') }}"
                class="{{ $tabClasses }} {{ $activeFilter === 'finished' ? 'border-blue-400 text-blue-400' : 'border-transparent text-gray-400 hover:border-gray-500' }}">
                Resultados Finalizados
            </a>

            {{-- PESTAÑA 3: TODOS --}}
            <a href="{{ $getCalendarRoute('all') }}"
                class="{{ $tabClasses }} {{ $activeFilter === 'all' ? 'border-yellow-400 text-yellow-400' : 'border-transparent text-gray-400 hover:border-gray-500' }}">
                Histórico Completo
            </a>
        </div>

        {{-- ⬇️ 2. CONTENIDO DEL CALENDARIO (Agrupado y filtrado) ⬇️ --}}
        <div class="space-y-8">
            @forelse($matchesByDate as $dateString => $matchesOnDate)
                @php
                    $date = \Carbon\Carbon::parse($dateString);
                    $isPast = $date->isPast() && !$date->isToday();
                @endphp

                <div class="card p-4 shadow-lg border-t-4 {{ $isPast ? 'border-gray-600' : 'border-primary' }}">

                    {{-- DÍA DE ENCABEZADO --}}
                    <h3 class="text-2xl font-bold mb-4 {{ $isPast ? 'text-gray-400' : 'text-white' }}">
                        {{ $date->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                        @if ($date->isToday())
                            <span class="text-sm text-red-500 font-normal ml-2">(HOY)</span>
                        @endif
                    </h3>

                    {{-- LISTADO DE PARTIDOS EN ESE DÍA --}}
                    <div class="space-y-3">
                        @foreach ($matchesOnDate as $match)
                            @php
                                $statusColor = $match->estado === 'finalizado' ? 'bg-blue-600' : 'bg-primary';
                                $score =
                                    $match->estado === 'finalizado'
                                        ? "{$match->goles_local} - {$match->goles_visitante}"
                                        : $match->fecha_hora->format('H:i');
                            @endphp

                            <div class="p-3 bg-gray-800 rounded-lg shadow-md">

                                {{-- ENCABEZADO DE RESULTADO Y ESTADO --}}
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-2">

                                    {{-- Equipos --}}
                                    <span class="font-medium text-white mb-2 sm:mb-0 w-full sm:w-1/2">
                                        <img src="{{ $match->localTeam->escudo_url ?? asset('images/placeholder.png') }}"
                                            class="w-6 h-6 inline-block rounded-full mr-2">
                                        {{ $match->localTeam->nombre ?? 'N/A' }} <span
                                            class="text-gray-400 font-normal">vs</span>
                                        <img src="{{ $match->visitorTeam->escudo_url ?? asset('images/placeholder.png') }}"
                                            class="w-6 h-6 inline-block rounded-full mr-2">
                                        {{ $match->visitorTeam->nombre ?? 'N/A' }}
                                    </span>

                                    {{-- Resultado / Hora / Estado --}}
                                    <div class="flex items-center space-x-3 text-right">
                                        <span
                                            class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusColor }} text-white">
                                            {{ ucfirst($match->estado) }}
                                        </span>
                                        <span class="text-lg font-bold text-yellow-400">
                                            {{ $score }}
                                        </span>

                                        {{-- Botón de Acción --}}
                                        @if ($match->estado === 'pendiente' && session('is_admin'))
                                            <a href="{{ route('admin.finalize-match', ['match_id' => $match->id]) }}"
                                                class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded-md text-xs">Finalizar</a>
                                        @endif
                                    </div>
                                </div>

                                {{-- ⬇️ CUERPO DE EVENTOS DETALLADOS (SOLO SI FINALIZADO) ⬇️ --}}
                                @if ($match->estado === 'finalizado')
                                    <div class="pt-3 mt-3 border-t border-gray-700">
                                        <h5 class="text-sm font-semibold text-gray-400 mb-2">Detalle de Eventos</h5>

                                        <div class="flex text-xs text-gray-400">

                                            {{-- Columna Local --}}
                                            <div class="w-1/2 pr-2 space-y-1 border-r border-gray-600">
                                                @php
                                                    $localEvents = $match->eventos
                                                        ->where('equipo_id', $match->equipo_local_id)
                                                        ->sortBy('minuto');
                                                @endphp

                                                @forelse($localEvents as $event)
                                                    @include('partials.event_item', [
                                                        'event' => $event,
                                                        'align' => 'right',
                                                    ])
                                                @empty
                                                    <div class="text-gray-500 text-right pr-2">Sin eventos</div>
                                                @endforelse
                                            </div>

                                            {{-- Columna Visitante --}}
                                            <div class="w-1/2 pl-2 space-y-1">
                                                @php
                                                    $visitorEvents = $match->eventos
                                                        ->where('equipo_id', $match->equipo_visitante_id)
                                                        ->sortBy('minuto');
                                                @endphp

                                                @forelse($visitorEvents as $event)
                                                    @include('partials.event_item', [
                                                        'event' => $event,
                                                        'align' => 'left',
                                                    ])
                                                @empty
                                                    <div class="text-gray-500 text-left pl-2">Sin eventos</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                @endif {{-- Fin de if finalizado --}}

                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-xl text-gray-400 text-center mt-10">No hay partidos que coincidan con el filtro
                    "{{ $activeFilter }}".</p>
            @endforelse
        </div>
    </div>
@endsection
