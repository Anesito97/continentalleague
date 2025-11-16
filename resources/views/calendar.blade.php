@extends('index')

@section('content')
    @php
        $activeJornada ??= 1;
        $allJornadas ??= collect([1]);
    @endphp                 
{{-- MEJORA: Título con gradiente --}}
<h2 class="text-3xl font-bold mb-6 border-b border-primary pb-2 flex items-center bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            <span class="material-symbols-outlined mr-3 text-3xl text-primary">calendar_month</span>
            Calendario de Partidos por Jornada
        </h2>

        {{-- ---------------------------------------------------- --}}
        {{-- 1. SELECTOR DE JORNADAS (IMPRESIONANTE Y RESPONSIVE) --}}
        {{-- ---------------------------------------------------- --}}
        {{-- MEJORA: "Glassmorphism" para el contenedor --}}
        <div class="bg-card-bg/80 backdrop-blur-lg border border-white/10 p-4 mb-6 shadow-xl rounded-lg">
            <h3 class="text-xl font-bold text-white mb-3">Navegar por Jornada</h3>
            <div class="flex flex-wrap gap-2 overflow-x-auto pb-2">
                @forelse($allJornadas as $jornada)
                    {{-- ⬇️ Botón de selección de jornada ⬇️ --}}
                    {{-- MEJORA: Gradiente para el botón activo --}}
                    <a href="{{ route('matches.calendar', ['status' => $activeFilter, 'jornada' => $jornada]) }}"
                        class="px-4 py-2 rounded-full font-semibold text-sm transition duration-300 flex-shrink-0
                              {{ $activeJornada === $jornada ? 'bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30' : 'bg-gray-700 text-gray-200 hover:bg-gray-600 hover:text-white' }}">
                        Jornada {{ $jornada }}
                    </a>
                @empty
                    <p class="text-gray-500">No hay jornadas programadas.</p>
                @endforelse
            </div>
        </div>

        {{-- ⬇️ 2. NAVEGACIÓN DE PESTAÑAS (TABS) - Mantener el filtro de estado ⬇️ --}}
        {{-- MEJORA: Pestañas estilo "Píldora" (Pills) --}}
        <div class="flex space-x-2 sm:space-x-3 mb-6 overflow-x-auto">

            @php
                // Función auxiliar para mantener la jornada activa al cambiar el filtro
                $getFilteredRoute = fn($status) => route('matches.calendar', [
                    'status' => $status,
                    'jornada' => $activeJornada,
                ]);
                $tabClasses = 'px-4 py-2 text-sm font-semibold transition-all duration-300 rounded-md flex-shrink-0 ';
            @endphp

            <a href="{{ $getFilteredRoute('pending') }}"
                class="{{ $tabClasses }} {{ $activeFilter === 'pending' ? 'bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
                Pendientes
            </a>

            <a href="{{ $getFilteredRoute('finished') }}"
                class="{{ $tabClasses }} {{ $activeFilter === 'finished' ? 'bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-500/30' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
                Finalizados
            </a>

            <a href="{{ $getFilteredRoute('all') }}"
                class="{{ $tabClasses }} {{ $activeFilter === 'all' ? 'bg-gradient-to-r from-yellow-500 to-amber-600 text-white shadow-lg shadow-yellow-500/30' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
                Histórico
            </a>
        </div>

        {{-- ⬇️ 3. CONTENIDO PRINCIPAL (Agrupado por Jornada) ⬇️ --}}
        <div class="space-y-8">

            {{-- Mostramos solo la jornada activa, ya que el filtro de jornada está aplicado --}}
            @forelse($matchesByJornada as $jornadaNumber => $matchesInJornada)
                @if ($jornadaNumber == $activeJornada)
                    {{-- MEJORA: Título de Jornada con gradiente --}}
                    <h3 class="text-3xl font-extrabold mb-4 border-b border-primary/50 pb-2 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
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

                        {{-- MEJORA: "Glassmorphism" para el contenedor de fecha --}}
                        <div class="bg-card-bg/80 backdrop-blur-lg border border-white/10 p-4 shadow-lg rounded-lg border-t-4 {{ $isPast ? 'border-gray-600' : 'border-primary' }}">

                            <h4 class="text-xl font-bold mb-3 {{ $isPast ? 'text-gray-400' : 'text-white' }}">
                                {{ $date->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                                @if ($date->isToday())
                                    <span class="text-sm text-red-500 font-normal ml-2">(HOY)</span>
                                @endif
                            </h4>

                            {{-- LISTADO DE PARTIDOS --}}
                            <div class="space-y-3">
                                @foreach ($matchesOnDate as $match)
                                    {{-- MEJORA: Reutilizamos el partial de match_card que ya mejoramos --}}
                                    @include('partials.calendar_match_item', ['match' => $match]) 
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            @empty
                {{-- MEJORA: Mensaje de "vacío" con "Glassmorphism" --}}
                <div class="bg-card-bg/80 backdrop-blur-lg rounded-lg shadow-xl p-8 border border-white/10 text-center">
                    <p class="text-xl text-gray-400">No hay partidos programados para la Jornada
                        {{ $activeJornada }} con el filtro "{{ $activeFilter }}".</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection