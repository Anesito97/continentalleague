<div id="finalize-match-content" class="admin-content space-y-6">
    <h4 class="text-2xl font-semibold mb-4 text-green-400">Finalizar Partido y Registrar Eventos</h4>

    <div class="card p-6">
        <div class="mb-6">
            <label for="match-to-finalize" class="block text-sm font-medium text-gray-400 mb-2">
                Selecciona el partido a finalizar
            </label>
            <form method="GET" action="{{ route('admin.finalize-match') }}">
                <select id="match-to-finalize" name="match_id" onchange="this.form.submit()"
                    class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:ring-green-500 focus:border-green-500 transition">
                    <option value="">Selecciona un partido pendiente...</option>
                    @foreach($pendingMatches as $match)
                        <option value="{{ $match->id }}" @selected(request('match_id') == $match->id)>
                            Jornada {{ $match->jornada }}: {{ $match->localTeam->nombre }} vs {{ $match->visitorTeam->nombre }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @php $selectedMatch = $pendingMatches->firstWhere('id', request('match_id')); @endphp

        @if($selectedMatch)
            <form id="finalize-match-form" method="POST" action="{{ route('matches.finalize') }}" class="space-y-8">
                @csrf
                <input type="hidden" name="match_id" value="{{ $selectedMatch->id }}">

                <fieldset class="border border-gray-700 rounded-lg p-4">
                    <legend class="px-2 text-lg font-semibold text-gray-300">Marcador Final</legend>
                    <div class="flex items-center justify-center gap-4 sm:gap-8 mt-4 text-center">
                        <div class="flex-1">
                            <img src="{{ $selectedMatch->localTeam->escudo_url ?? '' }}" class="w-16 h-16 rounded-full mx-auto mb-2 border-2 border-primary/50">
                            <label class="font-bold text-lg text-white">{{ $selectedMatch->localTeam->nombre }}</label>
                            <input type="number" name="goles_local" value="{{ old('goles_local', 0) }}" min="0" required
                                class="mt-2 block w-full text-4xl font-bold bg-gray-800 border-2 border-gray-600 rounded-md text-green-400 text-center focus:ring-green-500 focus:border-green-500">
                        </div>
                        <span class="text-4xl font-black text-gray-500 pt-24">VS</span>
                        <div class="flex-1">
                            <img src="{{ $selectedMatch->visitorTeam->escudo_url ?? '' }}" class="w-16 h-16 rounded-full mx-auto mb-2 border-2 border-secondary/50">
                            <label class="font-bold text-lg text-white">{{ $selectedMatch->visitorTeam->nombre }}</label>
                            <input type="number" name="goles_visitor" value="{{ old('goles_visitor', 0) }}" min="0" required
                                class="mt-2 block w-full text-4xl font-bold bg-gray-800 border-2 border-gray-600 rounded-md text-white text-center focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </fieldset>
                
                <fieldset class="border border-gray-700 rounded-lg p-4">
                    <legend class="px-2 text-lg font-semibold text-gray-300">Registro de Eventos</legend>
                    <div id="events-container" class="space-y-4 mt-4">
                        {{-- Las filas de eventos se añadirán aquí dinámicamente --}}
                        @for ($i = 0; $i < 3; $i++) {{-- Empezamos con 3 filas por defecto --}}
                            @include('admin.partials.event_row', ['i' => $i, 'players' => $players, 'localTeam' => $selectedMatch->localTeam, 'visitorTeam' => $selectedMatch->visitorTeam])
                        @endfor
                    </div>
                    <button type="button" id="add-event-btn" class="mt-4 w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                        <i class="fa-solid fa-plus mr-2"></i> Añadir Otro Evento
                    </button>
                </fieldset>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                    <i class="fa-solid fa-flag-checkered mr-2"></i> Finalizar Partido y Calcular Puntos
                </button>
            </form>
        @else
            <div id="finalize-match-info" class="text-center text-gray-500 mt-8 py-8 border-2 border-dashed border-gray-700 rounded-lg">
                <i class="fa-solid fa-arrow-up-long text-4xl mb-4"></i>
                <p>Selecciona un partido pendiente para empezar a registrar los resultados.</p>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('events-container');
        const addBtn = document.getElementById('add-event-btn');
        let eventIndex = {{ $i ?? 2 }} + 1; // Inicia el contador desde el último índice renderizado

        // Función para manejar la visibilidad del tipo de gol
        function handleGoalTypeVisibility(selectElement) {
            const row = selectElement.closest('.event-row');
            const goalTypeSelect = row.querySelector('.goal-type-select');
            if (selectElement.value === 'Gol') {
                goalTypeSelect.classList.remove('hidden');
            } else {
                goalTypeSelect.classList.add('hidden');
            }
        }

        // Añadir nuevo evento
        if (addBtn) {
            addBtn.addEventListener('click', function() {
                const newRowHtml = `
                    <div class="event-row flex flex-col sm:flex-row gap-2 bg-gray-800 p-3 rounded-lg border border-gray-600">
                        <select name="events[${eventIndex}][event_type]" class="event-type-select w-full sm:w-1/4 ...">...</select>
                        <select name="events[${eventIndex}][goal_type]" class="goal-type-select hidden ...">...</select>
                        <select name="events[${eventIndex}][player_id]" class="w-full sm:flex-grow ...">...</select>
                        <input type="number" name="events[${eventIndex}][minuto]" placeholder="Min'" ...>
                        <button type="button" class="remove-event-btn ..."><i class="fa-solid fa-trash-can"></i></button>
                    </div>`.replace(/.../g, 'px-3 py-2 bg-gray-700 border-gray-600 rounded-md text-sm'); // Reemplazo simplificado

                // Para una implementación robusta, clonarías una plantilla o tendrías el HTML completo aquí
                const newRow = document.createElement('div');
                newRow.innerHTML = ``;
                // NOTA: Una implementación más avanzada usaría una <template> tag o fetch para obtener el parcial.
                // Por simplicidad aquí, se asume que podrías copiar el contenido del parcial y ajustar los índices.
                
                // Simulación de añadir una fila (necesitarás el contenido del parcial para que funcione)
                const firstRow = container.querySelector('.event-row');
                if (firstRow) {
                    const clone = firstRow.cloneNode(true);
                    // Actualizar los 'name' de los inputs/selects del clon
                    clone.querySelectorAll('[name]').forEach(el => {
                        el.name = el.name.replace(/\[\d+\]/, `[${eventIndex}]`);
                    });
                    clone.querySelector('.goal-type-select').classList.add('hidden'); // Resetear visibilidad
                    container.appendChild(clone);
                    eventIndex++;
                }
            });
        }

        // Delegación de eventos para eliminar filas y mostrar/ocultar tipo de gol
        if (container) {
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-event-btn')) {
                    e.target.closest('.event-row').remove();
                }
            });

            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('event-type-select')) {
                    handleGoalTypeVisibility(e.target);
                }
            });

            // Comprobar visibilidad al cargar la página (para datos de 'old' en caso de error de validación)
            container.querySelectorAll('.event-type-select').forEach(handleGoalTypeVisibility);
        }
    });
</script>