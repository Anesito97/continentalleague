<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Continental League</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    {{-- FUENTES Y CONFIGURACIÓN MODERNA (STITCH + LEXEND) --}}
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;700;900&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#10b981", // Tu verde
                        secondary: "#3b82f6", // Tu azul
                        "dark-bg": "#111827", // Fondo
                        "card-bg": "#1f2937", // Tarjeta
                    },
                    fontFamily: {
                        display: ["Lexend", "sans-serif"], // Nueva tipografía principal
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        lg: "0.75rem",
                        xl: "1rem",
                        full: "9999px",
                    },
                },
            },
        };
    </script>

    <style>
        /* ------------------------------------------------ */
        /* RESET Y FIX DE LOADER (CRÍTICO) */
        /* ------------------------------------------------ */
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        body.loading {
            overflow: hidden;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        body:not(.loading) {
            position: static;
            overflow-y: auto;
        }

        /* ------------------------------------------------ */
        /* ESTÉTICA GENERAL Y ADAPTACIÓN DE CLASES VIEJAS */
        /* ------------------------------------------------ */

        /* Adaptación de colores y fuente base */
        body {
            background-color: #111827;
            /* dark-bg */
            font-family: 'Lexend', sans-serif;
            color: #e5e7eb;
        }

        /* Estilos de Tarjeta (Ajustados a la nueva sombra profesional) */
        .card {
            background-color: #1f2937;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 20px rgba(0, 0, 0, 0.7);
        }

        /* Iconos de Material Symbols (Para que se vean rellenos) */
        .material-symbols-outlined {
            font-variation-settings: "FILL" 1, "wght" 400, "GRAD" 0, "opsz" 24;
        }

        /* LÓGICA DEL LOADER */
        body.loading #app-container {
            opacity: 0;
            visibility: hidden;
        }

        body:not(.loading) #app-container {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.5s ease-in;
        }

        body.sidebar-open {
            overflow: hidden;
            position: fixed;
            width: 100%;
            top: var(--scroll-y, 0);
        }

        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #111827;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s ease-out;
        }

        .menu-icon {
            transition: transform 0.3s ease-in-out;
        }

        .menu-icon-active {
            /* border: 1px solid #ffffff98; */
            border-radius: 10%;
            padding: 10px;
            background: #ffffff57;
            transform: rotate(90deg) scale(0.8);
        }

        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.2);
            border-top: 4px solid #10b981;
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

        .swiper-button-prev,
        .swiper-button-next {
            color: #10b981 !important;
            /* Usar color primario */
            top: 50%;
            transform: translateY(-50%) scale(0.6);
            /* Reducir tamaño al 60% */
            width: 30px;
            /* Reducir ancho del área de clic */
            height: 30px;
            /* Reducir alto del área de clic */
            transition: transform 0.3s;
        }

        .swiper-button-prev:hover,
        .swiper-button-next:hover {
            transform: translateY(-50%) scale(0.8);
            /* Efecto sutil al pasar el ratón */
        }

        /* En móvil, ocultar flechas o hacerlas muy pequeñas */
        @media (max-width: 640px) {

            .swiper-button-prev,
            .swiper-button-next {
                display: none !important;
                /* Ocultar totalmente en móvil para no estorbar */
            }
        }
    </style>
</head>

<body class="bg-dark-bg text-white font-display loading">
    @php
        // Si el controlador no la pasó (ej. en una vista @yield), asumimos 'home'
        $activeView = $activeView ?? 'home';
    @endphp
    {{-- 1. OVERLAY DEL LOADER --}}
    <div id="loader-overlay">
        <div class="spinner"></div>
    </div>

    {{-- 2. CONTENEDOR PRINCIPAL: Incluye el Header Móvil y el Sidebar --}}
    <div id="app-container" class="flex flex-col sm:flex-row min-h-screen">

        {{-- ⬇️ HEADER MÓVIL (VISIBLE SÓLO EN PANTALLA PEQUEÑA) ⬇️ --}}
        <header class="w-full bg-gray-800 shadow-xl sm:hidden sticky top-0 z-20">
            <div class="flex justify-between items-center px-4 py-3">
                {{-- Título --}}
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                    <h1 class="text-xl font-bold">Continental League</h1>
                </div>

                {{-- BOTÓN DE TOGGLE --}}
                <button id="menu-toggle-button" onclick="toggleSidebar()"
                    class="text-white p-2 rounded-md hover:bg-white/10 transition">
                    <span id="menu-icon" class="material-symbols-outlined menu-icon">menu</span>
                </button>
            </div>
        </header>

        {{-- 1. ASIDE: BARRA DE NAVEGACIÓN LATERAL (Ahora toggleable y oculta en móvil por defecto) --}}
        {{-- Nota: El sidebar debe tener id="admin-sidebar" y la clase hidden para el toggle --}}
        @include('partials.sidebar')

        {{-- 2. MAIN CONTENT AREA --}}
        <main class="flex-1 p-4 sm:p-8 overflow-y-auto">
            <div class="max-w-7xl mx-auto">

                {{-- ⬇️ CONTENIDO DINÁMICO CENTRAL ⬇️ --}}
                @hasSection('content')
                    @yield('content')
                @else
                    {{-- PANELES NORMALES (HOME, STATS, ADMIN) --}}
                    <section id="home-view" data-view="home"
                        class="view-panel space-y-8 @if ($activeView !== 'home') hidden @endif">
                        @include('partials.standings', [
                            'teams' => $teams,
                            'recentMatches' => $recentMatches,
                        ])
                    </section>

                    <section id="stats-view" data-view="stats"
                        class="view-panel @if ($activeView !== 'stats') hidden @endif space-y-8">
                        @include('partials.stats', [
                            'topScorers' => $topScorers,
                            'topAssists' => $topAssists,
                            'topKeepers' => $topKeepers,
                        ])
                    </section>

                    <section id="admin-view" data-view="admin"
                        class="view-panel @if ($activeView !== 'admin') hidden @endif">
                        @include('admin.panel', [
                            'teams' => $teams,
                            'players' => $players,
                            'pendingMatches' => $pendingMatches,
                            'activeAdminContent' => session('activeAdminContent', 'teams'),
                        ])
                    </section>
                @endif
            </div>
        </main>
    </div>

    {{-- MODALES Y SCRIPTS FINALES --}}
    @include('partials.alerts')

    {{-- MODAL DE LOGIN (AÑADIDO CONTENIDO FALTANTE para solucionar la opacidad) --}}
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

    {{-- MODAL GENÉRICO DE EDICIÓN (Placeholder) --}}
    <div id="edit-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 hidden items-center justify-center">
    </div>


    {{-- 4. SCRIPTS FINALES (Toggle de Sidebar y Manejo de Loader) --}}
    <script>
        // Lógica del Loader
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.body.classList.remove('loading');
                window.scrollTo(0, 0);
                document.getElementById('loader-overlay').style.opacity = '0';
                setTimeout(function() {
                    const overlay = document.getElementById('loader-overlay');
                    if (overlay) overlay.style.display = 'none';
                }, 500);
            }, 300);
        });

        // ⬇️ FUNCIÓN CORREGIDA PARA EL SCROLL FIJO DEL SIDEBAR ⬇️
        function toggleSidebar() {
            const body = document.body;
            const sidebar = document.getElementById('admin-sidebar');
            const menuIcon = document.getElementById('menu-icon'); // El icono
            const scrollY = window.scrollY;

            if (sidebar.classList.contains('hidden')) {
                // --- ABRIR MENÚ ---

                // 1. ANIMACIÓN DEL ICONO
                menuIcon.textContent = 'close'; // Cambia el símbolo a 'close'
                menuIcon.classList.add('menu-icon-active'); // Aplica la rotación/escala

                // 2. SCROLL INTELIGENTE Y FIJADO
                body.style.setProperty('--scroll-y', `-${scrollY}px`);
                body.classList.add('sidebar-open');

                // 3. Mostrar el sidebar
                sidebar.classList.remove('hidden');

            } else {
                // --- CERRAR MENÚ ---

                // 1. ANIMACIÓN DEL ICONO
                menuIcon.textContent = 'menu'; // Cambia el símbolo a 'menu'
                menuIcon.classList.remove('menu-icon-active'); // Quita la rotación

                // 2. SCROLL INTELIGENTE Y LIBERACIÓN
                const scrollYValue = body.style.getPropertyValue('--scroll-y');
                const scrollPosition = parseInt(scrollYValue.replace('px', '')) * -1;

                // 3. Ocultar visualmente el sidebar
                sidebar.classList.add('hidden');

                // 4. Quitar la clase de bloqueo (liberando el body)
                body.classList.remove('sidebar-open');

                // 5. Restaurar la posición del scroll
                window.scrollTo(0, scrollPosition);
            }
        }

        // Funciones esenciales (navigate, manejo de alertas)
        @if (session('success') || session('error') || $errors->any())
            // ... (cuerpo de manejo de alertas) ...
        @endif
        function navigate(route) {
            window.location.href = route;
        }

        document.addEventListener('DOMContentLoaded', function() {

            if (typeof Swiper !== 'undefined') {
                new Swiper('.news-swiper', {
                    loop: true,

                    // ⬇️ AJUSTES PARA EL EFECTO ESPECTACULAR (Ver la otra venir) ⬇️
                    slidesPerView: 1.1, // Muestra una diapositiva completa y parte de la siguiente (1.1)
                    spaceBetween: 20, // Espacio entre slides
                    centeredSlides: true, // Centra el slide activo (opcional)

                    // Auto-Play
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },

                    // Si quieres el efecto 3D:
                    /*
                    effect: 'coverflow',
                    coverflowEffect: {
                        rotate: 50,
                        stretch: 0,
                        depth: 100,
                        modifier: 1,
                        slideShadows: true,
                    },
                    */

                    // Paginación y Navegación (con las flechas reducidas por el CSS)
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                });
            }
        });
    </script>

</body>

</html>
