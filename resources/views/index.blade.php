<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Continental League</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ------------------------------------------------ */
        /* RESET Y ESTILOS BASE (Manejo de scroll y altura) */
        /* ------------------------------------------------ */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        /* Reseteo para evitar márgenes predeterminados */
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        /* ------------------------------------------------ */
        /* ESTÉTICA GENERAL */
        /* ------------------------------------------------ */
        :root {
            --color-primary: #10b981;
            --color-secondary: #3b82f6;
            --color-dark-bg: #111827;
            --color-card-bg: #1f2937;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-dark-bg);
            color: #e5e7eb;
            -webkit-font-smoothing: antialiased;
            -moz-osx-moz-smoothing: grayscale;
        }

        /* Estilos de Tarjeta (Mantener) */
        .card {
            background-color: var(--color-card-bg);
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 20px rgba(0, 0, 0, 0.7);
        }

        /* ... (Otros estilos de formularios, etc., se asume que están correctos) ... */


        /* ------------------------------------------------ */
        /* LÓGICA DEL LOADER (CRÍTICA PARA EL SCROLL) */
        /* ------------------------------------------------ */

        /* FIX DEFINITIVO: Fija el body durante la carga para evitar el scroll */
        body.loading {
            overflow: hidden;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Restaura el flujo normal al terminar la carga */
        body:not(.loading) {
            position: static;
            overflow-y: auto;
        }

        /* Ocultar el contenido principal con transición */
        body.loading #app-container {
            opacity: 0;
            visibility: hidden;
        }

        body:not(.loading) #app-container {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.5s ease-in;
        }

        /* Loader Overlay (Mantener) */
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--color-dark-bg);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s ease-out;
        }

        /* Estilos del spinner (Mantener) */
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.2);
            border-top: 4px solid var(--color-primary);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-100">
    {{-- ⬇️ 2. OVERLAY DEL LOADER ⬇️ --}}
    <div id="loader-overlay">
        <div class="spinner"></div>
    </div>

    <div id="app-container" class="flex flex-col">

        {{-- 1. HEADER (Navbar) --}}
        @include('partials.navbar')

        {{-- 2. MAIN CONTENT AREA --}}
        <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

            {{-- VISTA PÚBLICA: CLASIFICACIÓN (HOME) --}}
            <section id="home-view" data-view="home"
                class="view-panel space-y-8 @if ($activeView !== 'home') hidden @endif">
                @include('partials.standings', ['teams' => $teams, 'recentMatches' => $recentMatches])
            </section>

            {{-- VISTA PÚBLICA: ESTADÍSTICAS (STATS) --}}
            <section id="stats-view" data-view="stats"
                class="view-panel @if ($activeView !== 'stats') hidden @endif space-y-8">
                @include('partials.stats', [
                    'topScorers' => $topScorers,
                    'topAssists' => $topAssists,
                    'topKeepers' => $topKeepers,
                ])
            </section>

            {{-- VISTA DE ADMINISTRACIÓN (ADMIN) --}}
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
        {{-- <footer class="bg-gray-800 mt-8 py-4 text-center text-gray-500 text-sm">
            Diseño Frontend con Tailwind CSS | Backend Laravel
        </footer> --}}
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

    {{-- MODAL GENÉRICO DE EDICIÓN (Necesario si mantienes edit.blade.php) --}}
    {{-- Este modal es solo un placeholder, la lógica de edición ocurre en la página 'edit' --}}
    <div id="edit-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 hidden items-center justify-center">
        <div class="bg-gray-800 card p-6 w-11/12 max-w-lg border-t-4 border-blue-500 shadow-2xl hover:transform-none">
            <h4 class="text-xl font-bold mb-4 text-blue-400">Edición Rápida (Deshabilitada)</h4>
            <p class="text-gray-300">La edición ahora se realiza en una página separada para simplificar. Presiona
                "Editar" para ir a la página de edición.</p>
            <button onclick="document.getElementById('edit-modal').classList.add('hidden')"
                class="mt-4 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition w-full">Cerrar</button>
        </div>
    </div>



    {{-- 3. SCRIPT DE CIERRE DEL LOADER --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Espera un pequeño retraso (ej. 300ms) para que el spinner sea visible, luego lo oculta.
            setTimeout(function() {
                document.body.classList.remove('loading');
                document.getElementById('loader-overlay').style.opacity = '0';

                // Opcional: Eliminar el overlay del DOM después de la transición
                setTimeout(function() {
                    const overlay = document.getElementById('loader-overlay');
                    if (overlay) overlay.style.display = 'none';
                }, 500);
            }, 300); // Retraso de 300ms antes de empezar a ocultar
        });
    </script>
    {{-- SCRIPT ESENCIAL PARA SESIÓN Y ALERTAS --}}
    <script>
        // =======================================================
        // 1. Manejo de Mensajes de Sesión (Toast/Modal)
        // =======================================================
        @if (session('success') || session('error') || $errors->any())
            const modal = document.getElementById('custom-alert');
            const titleElement = document.getElementById('alert-title');
            const messageElement = document.getElementById('alert-message');
            const modalButton = modal.querySelector('button');
            const modalCard = modal.querySelector('.card');

            const isError = @json(session()->has('error')) || @json($errors->any());

            titleElement.textContent = isError ? 'Error' : 'Éxito';

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
        // 2. Funciones de Navegación
        // =======================================================
        function navigate(route) {
            window.location.href = route;
        }
    </script>
</body>

</html>
