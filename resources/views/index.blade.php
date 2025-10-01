<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Liga de Fútbol</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ... (CSS Styles copied from original index.php) ... */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        :root {
            --color-primary: #10b981;
            --color-secondary: #3b82f6;
        }

        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-moz-smoothing: grayscale;
        }

        .card {
            background-color: #1f2937;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .admin-nav-item:hover {
            background-color: #374151;
            transition: background-color 0.2s;
        }

        #app-container {
            min-height: 100vh;
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-100">

    <div id="app-container" class="flex flex-col">

        {{-- 1. HEADER (Navbar) --}}
        @include('partials.navbar')

        {{-- 2. MAIN CONTENT AREA --}}
        <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

            {{-- VISTA PÚBLICA: CLASIFICACIÓN (HOME) --}}
            {{-- Visible solo si $activeView es 'home' --}}
            <section id="home-view" data-view="home"
                class="view-panel space-y-8 @if ($activeView !== 'home') hidden @endif">
                @include('partials.standings', ['teams' => $teams, 'recentMatches' => $recentMatches])
            </section>

            {{-- VISTA PÚBLICA: ESTADÍSTICAS (STATS) --}}
            {{-- Visible solo si $activeView es 'stats' --}}
            <section id="stats-view" data-view="stats"
                class="view-panel @if ($activeView !== 'stats') hidden @endif space-y-8">
                @include('partials.stats', [
                    'topScorers' => $topScorers,
                    'topAssists' => $topAssists,
                    'topKeepers' => $topKeepers,
                ])
            </section>

            {{-- VISTA DE ADMINISTRACIÓN (ADMIN) --}}
            {{-- Visible solo si $activeView es 'admin' --}}
            <section id="admin-view" data-view="admin"
                class="view-panel @if ($activeView !== 'admin') hidden @endif">
                @include('admin.panel', [
                    'teams' => $teams,
                    'players' => $players,
                    'pendingMatches' => $pendingMatches,
                    'activeAdminContent' => session('activeAdminContent', 'teams'), // Carga el contenido activo
                ])
            </section>

        </main>

        {{-- FOOTER --}}
        <footer class="bg-gray-800 mt-8 py-4 text-center text-gray-500 text-sm">
            Diseño Frontend con Tailwind CSS | Backend Laravel
        </footer>
    </div>

    {{-- 3. CUSTOM MESSAGE/ALERT MODAL --}}
    @include('partials.alerts')

    {{-- NUEVO MODAL DE LOGIN --}}
    <div id="login-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 hidden items-center justify-center">
        <div class="bg-gray-800 card p-6 w-11/12 max-w-sm border-t-4 border-green-500 shadow-2xl hover:transform-none">
            <h4 class="text-xl font-bold mb-4 text-green-400">Acceso de Administrador</h4>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="login-username" class="block text-sm font-medium text-gray-400">Usuario</label>
                    <input type="text" name="username" id="login-username" required
                        class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                </div>
                <div class="mb-6">
                    <label for="login-password" class="block text-sm font-medium text-gray-400">Contraseña</label>
                    <input type="password" name="password" id="login-password" required
                        class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                </div>
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition w-full mb-3">
                    Iniciar Sesión
                </button>
            </form>
            <button onclick="document.getElementById('login-modal').classList.add('hidden')"
                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition w-full">Cancelar</button>
        </div>
    </div>

    {{-- Script para mostrar la vista activa y manejar mensajes de sesión, y lógica de modales --}}
    {{-- <script>
        // =======================================================
        // 1. Manejo de Mensajes de Sesión (Toast/Modal)
        // =======================================================
        // Muestra el modal si existe un mensaje de sesión (success, error, o errores de validación)
        @if (session('success') || session('error') || $errors->any())
            const modal = document.getElementById('custom-alert');
            const titleElement = document.getElementById('alert-title');
            const messageElement = document.getElementById('alert-message');
            const modalButton = modal.querySelector('button');
            const modalCard = modal.querySelector('.card');

            // Determinar si hay un error de sesión o de validación
            const isError = @json(session()->has('error')) || @json($errors->any());

            titleElement.textContent = isError ? 'Error' : 'Éxito';

            // Manejar el mensaje (usar errores de validación si existen)
            @if ($errors->any())
                let errorHtml = '';
                @foreach ($errors->all() as $error)
                    errorHtml += '{{ $error }}<br>';
                @endforeach
                messageElement.innerHTML = errorHtml;
            @else
                messageElement.textContent = @json(session('error') ?? session('success'));
            @endif

            modalCard.classList.remove('border-green-500', 'border-red-500');
            titleElement.classList.remove('text-green-400', 'text-red-400');
            modalButton.classList.remove('bg-green-600', 'bg-red-600', 'hover:bg-green-700', 'hover:bg-red-700');

            if (isError) {
                titleElement.classList.add('text-red-400');
                modalCard.classList.add('border-red-500');
                modalButton.classList.add('bg-red-600', 'hover:bg-red-700');
            } else {
                titleElement.classList.add('text-green-400');
                modalCard.classList.add('border-green-500');
                modalButton.classList.add('bg-green-600', 'hover:bg-green-700');
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        @endif


        // =======================================================
        // 2. Funciones de Navegación (Reemplazo del JS original)
        // =======================================================
        function navigate(route) {
            window.location.href = route;
        }

        function toggleAuth() {
            @if (session('is_admin'))
                navigate('{{ route('logout') }}');
            @else
                // Abre el modal de login
                document.getElementById('login-modal').classList.remove('hidden');
            @endif
        }


        // =======================================================
        // 3. Lógica de Modales CRUD (Edición y Finalizar Partido)
        // =======================================================

        // Variables Blade inyectadas para JS
        const allTeams = @json($teams);
        const allPlayers = @json($players);

        // Función auxiliar para generar opciones de equipo (usada en edición y partidos)
        function getTeamOptions(selectedTeamId) {
            let options = '<option value="">Seleccionar Equipo...</option>';
            allTeams.forEach(team => {
                const selected = team.id === selectedTeamId ? 'selected' : '';
                options += `<option value="${team.id}" ${selected}>${team.nombre}</option>`;
            });
            return options;
        }

        // Función auxiliar para obtener opciones de jugador (usada en eventos)
        function getPlayerOptions() {
            return allPlayers.map(p =>
                `<option value="${p.id}">${p.nombre} (#${p.numero}) - ${p.equipo.nombre ?? 'N/A'}</option>`
            ).join('');
        }

        /**
         * Rellena y muestra el modal de edición.
         */
        function openEditModal(type, data) {
            const modal = document.getElementById('edit-modal');
            const title = document.getElementById('edit-modal-title');
            const form = document.getElementById('edit-modal-form');
            const container = document.getElementById('edit-fields-container');

            title.textContent = `Editar ${type.charAt(0).toUpperCase() + type.slice(1)}: ${data.nombre || data.name}`;
            container.innerHTML = '';

            // 1. Configurar la acción del formulario y el método (PUT)
            form.action = `/admin/${type}s/${data.id}`;

            // 2. Inyectar campos dinámicos
            if (type === 'player') {
                container.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Nombre</label>
                        <input type="text" name="nombre" value="${data.nombre}" required class="w-full px-3 py-2 bg-gray-700 rounded-md text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Número</label>
                        <input type="number" name="numero" value="${data.numero}" required min="1" max="99" class="w-full px-3 py-2 bg-gray-700 rounded-md text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Equipo</label>
                    <select name="equipo_id" required class="w-full px-3 py-2 bg-gray-700 rounded-md text-white">
                        ${getTeamOptions(data.equipo_id)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Posición</label>
                    <select name="posicion" required class="w-full px-3 py-2 bg-gray-700 rounded-md text-white">
                        ${['portero', 'defensa', 'medio', 'delantero'].map(pos => 
                            `<option value="${pos}" ${data.posicion === pos ? 'selected' : ''}>${pos.charAt(0).toUpperCase() + pos.slice(1)}</option>`
                        ).join('')}
                    </select>
                </div>
                <div class="border-t border-gray-600 pt-3">
                    <label class="block text-sm font-medium text-gray-400">Foto Actual</label>
                    <img src="${data.foto_url || 'https://placehold.co/100x100'}" class="w-16 h-16 rounded-full object-cover my-2">
                    <label class="block text-sm font-medium text-gray-400">Subir Nueva Foto (Sustituir)</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-300">
                </div>
            `;
            } else if (type === 'team') {
                container.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-400">Nombre</label>
                    <input type="text" name="nombre" value="${data.nombre}" required class="w-full px-3 py-2 bg-gray-700 rounded-md text-white">
                </div>
                <div class="border-t border-gray-600 pt-3">
                    <label class="block text-sm font-medium text-gray-400">Logo Actual</label>
                    <img src="${data.escudo_url || 'https://placehold.co/50x50'}" class="w-16 h-16 rounded-full object-cover my-2">
                    <label class="block text-sm font-medium text-gray-400">Subir Nuevo Logo (Sustituir)</label>
                    <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-300">
                </div>
            `;
            } else if (type === 'match') {
                container.innerHTML = `
                        <div>
                            <label class="block text-sm font-medium text-gray-400">Fecha</label>
                            <input type="date" name="date" value="${data.fecha_hora.substring(0, 10)}" required class="w-full px-3 py-2 bg-gray-700 rounded-md text-white">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-400">Hora</label>
                            <input type="time" name="time" value="${data.fecha_hora.substring(11, 16)}" required class="w-full px-3 py-2 bg-gray-700 rounded-md text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400">Equipo Local</label>
                            <select name="localId" required class="w-full px-3 py-2 bg-gray-700 rounded-md text-white">
                                ${getTeamOptions(data.equipo_local_id)}
                            </select>
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-400">Equipo Visitante</label>
                            <select name="visitorId" required class="w-full px-3 py-2 bg-gray-700 rounded-md text-white">
                                ${getTeamOptions(data.equipo_visitante_id)}
                            </select>
                        </div>
                        <input type="hidden" name="dateTime" id="match-dateTime-combined">
                         <script>
                            // Usar un script para combinar fecha y hora antes de enviar el formulario de edición
                            document.getElementById('edit-modal-form').addEventListener('submit', function(e) {
                                const dateInput = this.querySelector('input[name="date"]').value;
                                const timeInput = this.querySelector('input[name="time"]').value;
                                this.querySelector('input[name="dateTime"]').value = dateInput + ' ' + timeInput;
                            });
    </script>
    `;
    }

    modal.classList.remove('hidden');
    }

    /**
    * Añade una fila dinámica al formulario de registro de eventos (Finalizar Partido).
    * Esta función se llama desde admin/finalize.blade.php
    */
    function addEventRow() {
    const container = document.getElementById('events-container');
    const newRow = document.createElement('div');
    newRow.className = 'flex flex-col sm:flex-row gap-2 card p-3 border border-gray-600 transition duration-150';

    // Usamos un timestamp único como clave de array para Laravel
    const uniqueKey = Date.now();

    newRow.innerHTML = `
    <select name="events[${uniqueKey}][event_type]"
        class="w-full sm:w-1/3 px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm">
        <option value="Gol">Gol</option>
        <option value="Asistencia">Asistencia</option>
        <option value="Parada">Parada (Portero)</option>
        <option value="Amarilla">Tarjeta Amarilla</option>
        <option value="Roja">Tarjeta Roja</option>
    </select>
    <select name="events[${uniqueKey}][player_id]"
        class="w-full sm:flex-grow px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm">
        <option value="">Seleccionar Jugador...</option>
        ${getPlayerOptions()}
    </select>
    <input type="number" name="events[${uniqueKey}][minuto]" placeholder="Minuto"
        class="w-16 px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm">
    <button type="button" onclick="this.parentNode.remove()"
        class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-md text-sm font-semibold flex-shrink-0">X</button>
    `;
    container.appendChild(newRow);
    }

    /**
    * Rellena y muestra el formulario de Finalizar Partido (admin/finalize.blade.php)
    */
    function loadFinalizeForm(matchId) {
    const form = document.getElementById('finalize-match-form');
    const infoText = document.getElementById('finalize-match-info');
    const select = document.getElementById('match-to-finalize');
    const selectedOption = select.options[select.selectedIndex];

    if (!matchId) {
    form.classList.add('hidden');
    infoText.classList.remove('hidden');
    return;
    }

    document.getElementById('finalize-match-id').value = matchId;
    document.getElementById('local-team-name').textContent = selectedOption.dataset.localName;
    document.getElementById('visitor-team-name').textContent = selectedOption.dataset.visitorName;
    document.getElementById('events-container').innerHTML = '';

    addEventRow();

    form.classList.remove('hidden');
    infoText.classList.add('hidden');
    }
    </script> --}}

</body>

</html>
