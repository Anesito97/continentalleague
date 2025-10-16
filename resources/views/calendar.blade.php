@extends('index')

@section('content')
    @php
        $activeJornada ??= 1;
        $allJornadas ??= collect([1]);
    @endphp
 class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">                    
<h2 class="text-3xl font-bold text-white mb-6 border-b border-primary pb-2 flex items-center">
            <span class="material-symbols-outlined mr-3 text-3xl text-primary">calendar_month</span>
            Calendario de Partidos por Jornada
        </h2>

        {{-- ---------------------------------------------------- --}}
        {{-- 1. SELECTOR DE JORNADAS (IMPRESIONANTE Y RESPONSIVE) --}}
        {{-- ---------------------------------------------------- --}}
        <div class="card p-4 mb-6 shadow-xl border-t-4 border-gray-700">
            <h3 class="text-xl font-bold text-white mb-3">Navegar por Jornada</h3>
            <div class="flex flex-wrap gap-2 overflow-x-auto pb-2">
                @forelse($allJornadas as $jornada)
                    {{-- ⬇️ Botón de selección de jornada ⬇️ --}}
                    <a href="{{ route('matches.calendar', ['status' => $activeFilter, 'jornada' => $jornada]) }}"
                        class="px-4 py-2 rounded-full font-semibold text-sm transition duration-300 flex-shrink-0
                              {{ $activeJornada === $jornada ? 'bg-primary text-gray-900 shadow-lg' : 'bg-gray-700 text-white hover:bg-gray-600' }}">
                        Jornada {{ $jornada }}
                    </a>
                @empty
                    <p class="text-gray-500">No hay jornadas programadas.</p>
                @endforelse
            </div>
        </div>

        {{-- ⬇️ 2. NAVEGACIÓN DE PESTAÑAS (TABS) - Mantener el filtro de estado ⬇️ --}}
        <div class="flex space-x-3 mb-6 border-b border-gray-700 pb-1 overflow-x-auto">

            @php
                // Función auxiliar para mantener la jornada activa al cambiar el filtro
                $getFilteredRoute = fn($status) => route('matches.calendar', [
                    'status' => $status,
                    'jornada' => $activeJornada,
                ]);
                $tabClasses = 'px-4 py-2 text-sm font-semibold border-b-2 transition duration-300 ';
            @endphp

            <a href="{{ $getFilteredRoute('pending') }}"
                class="{{ $tabClasses }} {{ $activeFilter === 'pending' ? 'border-primary text-primary' : 'border-transparent text-gray-400 hover:border-gray-500' }}">
                Partidos Pendientes
            </a>

            <a href="{{ $getFilteredRoute('finished') }}"
                class="{{ $tabClasses }} {{ $activeFilter === 'finished' ? 'border-blue-400 text-blue-400' : 'border-transparent text-gray-400 hover:border-gray-500' }}">
                Resultados Finalizados
            </a>

            <a href="{{ $getFilteredRoute('all') }}"
                class="{{ $tabClasses }} {{ $activeFilter === 'all' ? 'border-yellow-400 text-yellow-400' : 'border-transparent text-gray-400 hover:border-gray-500' }}">
                Histórico Completo
            </a>
        </div>

        {{-- ⬇️ 3. CONTENIDO PRINCIPAL (Agrupado por Jornada) ⬇️ --}}
        <div class="space-y-8">

            {{-- Mostramos solo la jornada activa, ya que el filtro de jornada está aplicado --}}
            @forelse($matchesByJornada as $jornadaNumber => $matchesInJornada)
                @if ($jornadaNumber == $activeJornada)
                    <h3 class="text-3xl font-extrabold text-white mb-4 border-b border-primary/50 pb-2">
                        Jornada {{ $jornadaNumber }}
                    </h3>

                    {{-- Iterar sobre los partidos de esta jornada, agrupados por fecha (si aplica) --}}
                    @php
                        // Agrupamos los partidos dentro de esta jornada por fecha para el display final
                        $matchesByDate = $matchesInJornada->groupBy(
                            fn($m) => \Carbon\Carbon::parse($m->fecha_hora)->format('Y-m-d'),
                        );
                    @endphp

                    @foreach ($matchesByDate as $dateString => $matchesOnDate)
                        @php
                            $date = \Carbon\Carbon::parse($dateString);
                            $isPast = $date->isPast() && !$date->isToday();
                        @endphp

                        <div class="card p-4 shadow-lg border-t-4 {{ $isPast ? 'border-gray-600' : 'border-primary' }}">

                            <h4 class="text-xl font-bold mb-3 {{ $isPast ? 'text-gray-400' : 'text-white' }}">
                                {{ $date->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                                @if ($date->isToday())
                                    <span class="text-sm text-red-500 font-normal ml-2">(HOY)</span>
                                @endif
                            </h4>

                            {{-- LISTADO DE PARTIDOS --}}
                            <div class="space-y-3">
                                @foreach ($matchesOnDate as $match)
                                    @include('partials.calendar_match_item', ['match' => $match]) {{-- Usamos un partial para limpieza --}}
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            @empty
                <p class="text-xl text-gray-400 text-center mt-10">No hay partidos programados para la Jornada
                    {{ $activeJornada }} con el filtro "{{ $activeFilter }}".</p>
            @endforelse
        </div>
    </div>
@endsection
