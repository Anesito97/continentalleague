<header class="bg-gray-800 shadow-xl sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
        <h1 class="text-2xl font-extrabold text-white tracking-tight">
            <span class="text-green-400">Liga</span> Manager
        </h1>
        <nav class="flex space-x-4 items-center">
            {{-- Botones de Navegación Pública (Usando <a>) --}}
            <a href="{{ route('home') }}"
                class="text-gray-300 hover:text-green-400 transition font-medium">Clasificación</a>
            <a href="{{ route('home') }}?view=stats"
                class="text-gray-300 hover:text-green-400 transition font-medium">Estadísticas</a>

            <div id="auth-controls" class="flex items-center space-x-2">
                @if (session('is_admin'))
                    {{-- Opción 1: Cerrar Sesión --}}
                    <a href="{{ route('logout') }}"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-full text-sm font-semibold transition">
                        Cerrar Sesión
                    </a>
                    {{-- Opción 2: Ir al Panel Admin --}}
                    <a href="{{ route('admin.panel') }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-full text-sm font-semibold transition">
                        Ir al Panel (Admin)
                    </a>
                @else
                    {{-- Botón de Login (Usará un formulario simple en línea en lugar de modal) --}}
                    <form method="POST" action="{{ route('login') }}" class="flex space-x-2 items-center">
                        @csrf
                        <input type="text" name="username" placeholder="Usuario" required 
                               class="px-2 py-1 bg-gray-700 border border-gray-600 rounded-md text-sm w-24">
                        <input type="password" name="password" placeholder="Contraseña" required 
                               class="px-2 py-1 bg-gray-700 border border-gray-600 rounded-md text-sm w-24">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-full text-sm font-semibold transition">
                            Login
                        </button>
                    </form>
                @endif
            </div>
        </nav>
    </div>
</header>