<header class="bg-gray-800 shadow-xl sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">

        {{-- CONTENEDOR FLEXIBLE PARA EL TÍTULO Y EL BOTÓN DE MENÚ --}}
        <div class="flex justify-between items-center">

            {{-- Contenedor del Logo y Título --}}
            <div class="flex items-center space-x-3">

                {{-- LOGO DE LA LIGA --}}
                <img src="{{ asset('logo.png') }}" alt="Continental League Logo"
                    class="w-8 h-8 sm:w-10 sm:h-10 object-contain">

                {{-- TÍTULO --}}
                <h1 class="text-2xl font-extrabold text-white tracking-tight">
                    <span class="text-green-400">Continental</span> League
                </h1>
            </div>

            {{-- BOTÓN DE MENÚ (Visible solo en pantallas pequeñas) --}}
            <button id="menu-button" onclick="document.getElementById('nav-menu').classList.toggle('hidden')"
                class="sm:hidden text-gray-300 hover:text-green-400 p-2 rounded-md transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
            </button>
        </div>

        {{-- MENÚ DE NAVEGACIÓN (Oculto por defecto en móvil, visible en SM y más grandes) --}}
        <nav id="nav-menu" class="hidden sm:block mt-3 sm:mt-0">

            {{-- Contenedor de elementos del menú (En móvil se apila verticalmente, en SM/LG es horizontal) --}}
            <div class="flex flex-col sm:flex-row sm:space-x-4 sm:items-center space-y-2 sm:space-y-0 pb-1">

                {{-- Navegación Pública --}}
                <a href="{{ route('home') }}"
                    class="text-gray-300 hover:text-green-400 transition font-medium">Clasificación y Partidos</a>
                <a href="{{ route('home') }}?view=stats"
                    class="text-gray-300 hover:text-green-400 transition font-medium">Estadísticas</a>

                {{-- Controles de Autenticación --}}
                <div id="auth-controls" class="pt-2 sm:pt-0">
                    @if (session('is_admin'))
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-x-2 sm:space-y-0">
                            {{-- Opción 1: Cerrar Sesión --}}
                            <a href="{{ route('logout') }}"
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-full text-sm font-semibold text-center transition">
                                Cerrar Sesión
                            </a>
                            {{-- Opción 2: Ir al Panel Admin --}}
                            <a href="{{ route('admin.panel') }}"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-full text-sm font-semibold text-center transition">
                                Ir al Panel (Admin)
                            </a>
                        </div>
                    @else
                        {{-- Formulario de Login (Ajustado para ocupar menos espacio en móvil/desktop) --}}
                        <form method="POST" action="{{ route('login') }}"
                            class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 items-stretch sm:items-center">
                            @csrf
                            <input type="text" name="username" placeholder="Usuario" required
                                class="px-2 py-1 bg-gray-700 border border-gray-600 rounded-md text-sm sm:w-24">
                            <input type="password" name="password" placeholder="Contraseña" required
                                class="px-2 py-1 bg-gray-700 border border-gray-600 rounded-md text-sm sm:w-24">
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-full text-sm font-semibold transition">
                                Login
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </nav>
    </div>
</header>