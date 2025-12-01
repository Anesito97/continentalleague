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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                    // NUEVO: Añadimos una sombra de "glow"
                    boxShadow: {
                        'glow': '0 0 15px 0 rgba(16, 185, 129, 0.4)', // Sombra con color primario
                    }
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
            /* MEJORA: Fondo con gradiente radial sutil para dar profundidad */
            background-color: #111827;
            /* dark-bg */
            background-image: radial-gradient(ellipse at top center, #232a3b 0%, #111827 70%);
            font-family: 'Lexend', sans-serif;
            color: #e5e7eb;
        }

        /* Estilos de Tarjeta (Ajustados a la nueva sombra profesional) */
        .card {
            background-color: #1f2937;
            border-radius: 0.75rem;
            /* MEJORA: Sombra más oscura y borde más nítido */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            /* MEJORA: Sombra de "glow" al pasar el ratón */
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.7), 0 0 20px rgba(16, 185, 129, 0.3);
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
            /* MEJORA: Efecto cristal para el loader */
            background-color: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
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
            /* MEJORA: Gradiente para el spinner */
            border-top-color: transparent;
            border-left-color: #10b981;
            border-right-color: #10b981;
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

        /* --- TOQUE ESPECIAL: Borde animado para Modales --- */
        .modal-card-glow {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-card-glow::before {
            content: '';
            position: absolute;
            top: 0;
            left: -50%; /* Empezar fuera de la vista */
            width: 100%;
            height: 3px; /* Grosor del brillo */
            background: linear-gradient(90deg, transparent, #10b981, #3b82f6, transparent);
            animation: anim-glow 4s linear infinite;
            box-shadow: 0 0 10px #10b981;
        }

        @keyframes anim-glow {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(200%); /* Moverse a través y salir */
            }
        }
        /* --- Fin del Toque Especial --- */
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
        {{-- MEJORA: "Glassmorphism" (efecto cristal) para el header pegajoso --}}
        <header
            class="w-full bg-gray-900/80 backdrop-blur-lg border-b border-white/10 shadow-xl sm:hidden sticky top-0 z-20">
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
                @yield('content')
            </div>
        </main>
    </div>

    {{-- MODALES Y SCRIPTS FINALES --}}
    @include('partials.alerts')

    {{-- MODAL DE LOGIN (AÑADIDO CONTENIDO FALTANTE para solucionar la opacidad) --}}
    {{-- MEJORA: Fondo del modal con efecto blur --}}
        <div class="bg-gray-800 card p-6 w-11/12 max-w-sm shadow-2xl hover:transform-none modal-card-glow">
            <h4 class="text-xl font-bold mb-4 text-green-400 text-center">Iniciar Sesión</h4>

            {{-- BOTÓN GOOGLE --}}
            <a href="{{ route('auth.google') }}"
                class="flex items-center justify-center gap-2 bg-white text-gray-700 font-bold py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg mb-6 w-full hover:bg-gray-100">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google Logo">
                Iniciar con Google
            </a>

            <div class="border-t border-gray-700 my-4"></div>

            <div class="text-center mb-4">
                <a href="{{ route('login') }}" class="text-gray-400 hover:text-white text-sm transition-colors">
                    ¿Eres administrador? Inicia sesión aquí
                </a>
            </div>

            {{-- MEJORA: Botón de cancelar más sutil --}}
            <button onclick="document.getElementById('login-modal').classList.add('hidden')"
                class="bg-gray-700 hover:bg-gray-600 text-gray-300 font-bold py-2 px-4 rounded-lg transition w-full">Cancelar</button>
        </div>

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
    </script>

</body>

</html>
