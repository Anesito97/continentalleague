@php
    // --- Lógica del controlador (sin cambios) ---
    $baseRouteName = match ($type) {
        'news' => 'news',
        'match' => 'matches',
        'team' => 'teams',
        'player' => 'players',
        default => 'error',
    };
    $updateRoute = $baseRouteName . '.update';
    $destroyRoute = $baseRouteName . '.destroy';
    $cancelRoute = 'admin.' . ($type === 'news' ? 'news.index' : $baseRouteName);
    $itemName = $item->nombre ?? ($item->titulo ?? 'ID ' . $item->id);
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar {{ ucfirst($type) }} | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        :root {
            --color-primary: #3b82f6;
            /* Azul como color principal */
            --color-secondary: #4b5563;
            /* Gris para acciones secundarias */
            --color-danger: #ef4444;
            /* Rojo para acciones destructivas */
            --color-dark-bg: #111827;
            --color-card-bg: #1f2937;
            --color-border: #374151;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-dark-bg);
            color: #e5e7eb;
        }

        /* ✨ MEJORA: Estilo base para todos los inputs y selects */
        .form-input {
            background-color: #374151;
            border: 1px solid var(--color-border);
            border-radius: 0.375rem;
            /* rounded-md */
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            width: 100%;
            padding: 0.5rem 0.75rem;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.4);
        }
    </style>
</head>

<body class="antialiased">
    <div class="container mx-auto max-w-4xl px-4 py-12">
        <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="border-b border-gray-700 pb-4 mb-6">
                    <h1 class="text-3xl font-bold text-white leading-tight">Editar {{ ucfirst($type) }}</h1>
                    <p class="text-gray-400 mt-1">Modificando: <span
                            class="font-semibold text-primary">{{ $itemName }}</span></p>
                </div>

                @include('partials.alerts')

                <form method="POST" action="{{ route($updateRoute, $item->id) }}" enctype="multipart/form-data"
                    class="space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- ========= RENDERIZADO CONDICIONAL DE FORMULARIOS ========= --}}

                    {{-- FORMULARIO DE EQUIPO (REDiseñado) --}}
                    @if ($type === 'team')
                        <fieldset class="border border-gray-700 rounded-lg p-4">
                            <legend class="px-2 text-lg font-semibold text-gray-300">Datos del Equipo</legend>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div class="md:col-span-2">
                                    <label for="nombre"
                                        class="block text-sm font-medium text-gray-400 mb-1">Nombre</label>
                                    <input type="text" id="nombre" name="nombre"
                                        value="{{ old('nombre', $item->nombre) }}" required class="form-input">
                                </div>

                                <div class="md:col-span-2 flex items-center gap-6">
                                    <div class="flex-shrink-0">
                                        <p class="text-sm font-medium text-gray-400 mb-2">Logo Actual</p>
                                        <img id="logo-preview"
                                            src="{{ $item->escudo_url ?? 'https://placehold.co/100x100/374151/FFFFFF?text=LOGO' }}"
                                            class="w-20 h-20 rounded-full object-cover border-4 border-gray-600">
                                    </div>
                                    <div class="flex-grow">
                                        <label for="logo-upload"
                                            class="block text-sm font-medium text-gray-400 mb-2">Subir Nuevo
                                            Logo</label>
                                        <label for="logo-upload"
                                            class="w-full flex items-center justify-center px-4 py-3 bg-gray-700 border-2 border-dashed border-gray-500 rounded-lg cursor-pointer hover:bg-gray-600 hover:border-primary transition">
                                            <span id="logo-filename" class="text-gray-400 text-sm">Seleccionar un
                                                archivo...</span>
                                        </label>
                                        <input id="logo-upload" type="file" name="logo" accept="image/*"
                                            class="hidden">
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        {{-- FORMULARIO DE JUGADOR (Usa el parcial mejorado) --}}
                    @elseif($type === 'player')
                        @include('admin.forms.player_edit', [
                            'player' => $item,
                            'teams' => $teams,
                            'positions' => $positions,
                        ])

                        {{-- FORMULARIO DE PARTIDO (Usa el parcial mejorado) --}}
                    @elseif($type === 'match')
                        @include('admin.forms.match_edit', ['match' => $item, 'teams' => $teams])

                        {{-- FORMULARIO DE NOTICIAS --}}
                    @elseif($type === 'news')
                        @include('admin.forms.news_edit', ['item' => $item])
                    @endif

                    <div class="flex justify-end items-center gap-4 pt-6 border-t border-gray-700">
                        <a href="{{ route($cancelRoute) }}"
                            class="px-5 py-2.5 text-sm font-medium text-gray-300 bg-gray-600 rounded-lg hover:bg-gray-700 transition">Cancelar</a>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">Guardar
                            Cambios</button>
                    </div>
                </form>
            </div>

            <div class="bg-red-900/20 border-t border-red-500/30 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                    <div>
                        <h3 class="text-xl font-bold text-red-400">Zona de Peligro</h3>
                        <p class="text-red-300/80 mt-1 text-sm">Esta acción es irreversible y eliminará permanentemente
                            el registro.</p>
                    </div>
                    <form method="POST" action="{{ route($destroyRoute, $item->id) }}"
                        onsubmit="return confirm('ATENCIÓN: ¿Estás seguro de que quieres eliminar {{ $itemName }}? Esta acción no se puede deshacer.');"
                        class="mt-4 sm:mt-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition w-full sm:w-auto">
                            Eliminar {{ ucfirst($type) }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($type === 'match')
        <script>
            @php
                $templateString = view('admin.partials.event_row_edit', [
                    'i' => '__INDEX__',
                    'event' => null,
                    'localTeam' => $item->localTeam,
                    'visitorTeam' => $item->visitorTeam,
                ])->render();
            @endphp

            const eventRowTemplateHtml = @json($templateString);

            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('events-container');
                const addBtn = document.getElementById('add-event-btn');

                if (!container) return;

                let eventIndex = container.children.length;

                function handleGoalTypeVisibility(selectElement) {
                    const row = selectElement.closest('.event-row');
                    if (!row) return;
                    const goalTypeSelect = row.querySelector('.goal-type-select');
                    if (selectElement.value === 'Gol') {
                        goalTypeSelect.classList.remove('hidden');
                    } else {
                        goalTypeSelect.classList.add('hidden');
                    }
                }

                if (addBtn) {
                    addBtn.addEventListener('click', function() {
                        const newRowHtml = eventRowTemplateHtml.replace(/__INDEX__/g, eventIndex);
                        const newRowWrapper = document.createElement('div');
                        newRowWrapper.innerHTML = newRowHtml.trim();
                        container.appendChild(newRowWrapper.firstChild);
                        eventIndex++;
                    });
                }

                if (container) {
                    container.addEventListener('click', function(e) {
                        const removeBtn = e.target.closest('.remove-event-btn');
                        if (removeBtn) {
                            removeBtn.closest('.event-row').remove();
                        }
                    });

                    container.addEventListener('change', function(e) {
                        if (e.target.classList.contains('event-type-select')) {
                            handleGoalTypeVisibility(e.target);
                        }
                    });

                    container.querySelectorAll('.event-type-select').forEach(handleGoalTypeVisibility);
                }
            });
        </script>
    @endif
</body>

</html>
