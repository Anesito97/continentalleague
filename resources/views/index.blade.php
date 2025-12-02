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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
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

        .tooltip-container {
            position: relative;
            display: inline-block;
            cursor: pointer;
            /* Indica que es interactivo */
        }

        .tooltip-content {
            visibility: hidden;
            opacity: 0;
            width: 250px;
            /* Ancho fijo para el contenido */
            background-color: #374151;
            /* Gris oscuro para el fondo */
            color: #e5e7eb;
            text-align: left;
            border-radius: 6px;
            padding: 10px;
            position: absolute;
            z-index: 100;
            bottom: 125%;
            /* Posiciona encima del elemento */
            left: 50%;
            margin-left: -125px;
            /* Centrar el tooltip */
            transition: opacity 0.3s, visibility 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            font-family: sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        /* Mostrar el tooltip al hacer hover */
        .tooltip-container:hover .tooltip-content {
            visibility: visible;
            opacity: 1;
        }

        /* Pequeña flecha para indicar la conexión */
        .tooltip-content::after {
            content: "";
            position: absolute;
            top: 100%;
            /* En la parte inferior */
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #374151 transparent transparent transparent;
        }

        /* Ajuste específico para móvil (opcional): hacerlo más pequeño */
        @media (max-width: 640px) {
            .tooltip-content {
                width: 200px;
                left: 0%;
                margin-left: 0;
            }

            .tooltip-content::after {
                left: 10%;
            }
        }

        .pie-chart {
            border-radius: 50%;
            position: relative;
            width: 60px;
            /* Tamaño del círculo */
            height: 60px;
            z-index: 10;
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

    {{-- MODAL DE LOGIN (RESTAURADO PARA HOME PAGE) --}}
    <div id="login-modal" style="display: none;"
        class="fixed inset-0 bg-black/70 backdrop-blur-sm z-[60] hidden items-center justify-center p-4">
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
            <button onclick="document.getElementById('login-modal').style.display = 'none'; document.getElementById('login-modal').classList.add('hidden')"
                class="bg-gray-700 hover:bg-gray-600 text-gray-300 font-bold py-2 px-4 rounded-lg transition w-full">Cancelar</button>
        </div>
    </div>

    <script>
        // ⬇️ FUNCIÓN GLOBAL PARA ABRIR EL MODAL DE LOGIN (HOME PAGE) ⬇️
        function openLoginModal() {
            const modal = document.getElementById('login-modal');
            if (modal) {
                modal.style.display = 'flex';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
    </script>



    {{-- MODAL GENÉRICO DE EDICIÓN (Placeholder) --}}
    <div id="edit-modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    </div>

    {{-- ⬇️ MODAL LIGHTBOX PARA VISUALIZACIÓN DE IMAGEN ⬇️ --}}
    {{-- MEJORA: Fondo del lightbox con más blur --}}
    <!-- <div id="lightbox-modal"
        class="fixed inset-0 bg-black/80 backdrop-blur-md z-[10000] hidden items-center justify-center cursor-pointer p-4"
        onclick="closeLightbox()">
        <div class="relative max-w-full max-h-full">
            <img id="lightbox-image" src="" alt="Imagen Ampliada"
                class="max-w-full max-h-screen object-contain rounded-lg shadow-2xl">
            <div id="lightbox-caption" class="text-white text-center p-3 absolute bottom-0 w-full bg-black/50"></div>
        </div>
    </div> -->


    {{-- 4. SCRIPTS FINALES (Toggle de Sidebar y Manejo de Loader) --}}
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        // Espera a que el documento esté listo
        document.addEventListener('DOMContentLoaded', (event) => {
            // Inicializa GLightbox
            const lightbox = GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true,
                zoomable: true,
                download: true,
                openEffect: 'zoom',
                closeEffect: 'fade',
                description: {
                    position: 'bottom',
                    moreText: false 
                }
            });
        });
    </script>
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

            // Check for login query param to open modal
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('login') === 'true') {
                if (typeof openLoginModal === 'function') {
                    openLoginModal();
                }
            }
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

            // Aseguramos que el script se ejecute después de que el DOM esté cargado
            if (typeof Swiper !== 'undefined') {
                new Swiper('.news-swiper', {
                    loop: true,

                    // Usamos un solo slide centrado para que el efecto se aplique correctamente
                    slidesPerView: 'auto',
                    centeredSlides: true,
                    spaceBetween: 50, // Espacio entre las tarjetas

                    // ⬇️ EFECTO COVERFLOW 3D ⬇️
                    effect: 'coverflow',
                    coverflowEffect: {
                        rotate: 30, // Grados de rotación lateral
                        stretch: 0, // Estiramiento (cero para mantener tamaño)
                        depth: 100, // Profundidad de perspectiva (qué tan lejos se ven)
                        modifier: 1, // Multiplicador del efecto
                        slideShadows: false, // Desactivar sombras de slide por defecto para usar las nuestras
                    },

                    // Auto-Play
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },

                    // Paginación y Navegación
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

        // ⬇️ FUNCIONES DE LIGHTBOX ⬇️
        // function openLightbox(imageUrl, title) {
        //     document.getElementById('lightbox-image').src = imageUrl;
        //     document.getElementById('lightbox-caption').textContent = title;
        //     document.getElementById('lightbox-modal').classList.add('flex');
        //     document.getElementById('lightbox-modal').classList.remove('hidden');
        //     document.body.classList.add('overflow-hidden'); // Bloquear scroll
        // }

        // function closeLightbox() {
        //     document.getElementById('lightbox-modal').classList.add('hidden');
        //     document.getElementById('lightbox-modal').classList.remove('flex');
        //     document.body.classList.remove('overflow-hidden'); // Restaurar scroll
        // }

        // // Escucha la tecla ESC para cerrar
        // document.addEventListener('keydown', function(e) {
        //     if (e.key === "Escape") {
        //         closeLightbox();
        //     }
        // });

        // ⬇️ FUNCIONES PARA EL MODAL DE BORRADO ⬇️
        function openDeleteModal(button) {
            // 1. Obtenemos la URL del botón que fue presionado
            const url = button.getAttribute('data-url');
            
            // 2. Asignamos esa URL al 'action' del formulario en el modal
            document.getElementById('delete-form').action = url;
            
            // 3. Mostramos el modal
            const modal = document.getElementById('delete-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            // Ocultamos el modal
            const modal = document.getElementById('delete-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>

</body>

</html>