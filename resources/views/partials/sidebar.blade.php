<aside id="admin-sidebar" {{-- MEJORA: "Glassmorphism" aplicado al sidebar --}} class="w-3/4 sm:w-72 bg-gray-900/80 backdrop-blur-lg p-6 flex-col justify-between border-r border-white/10 shadow-2xl overflow-y-auto
              {{-- ⬇️ CLASES DE VISIBILIDAD CRÍTICAS (NO TOCAR) ⬇️ --}}
              fixed top-0 left-0 h-full z-50 sm:relative sm:flex hidden sm:flex">
    <div>
        {{-- Título y Logo --}}
        <div class="flex items-center gap-3 mb-8">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="w-10 h-10 object-contain">
            <h1 class="text-2xl font-bold">Continental League</h1>
        </div>

        {{-- Menú de Navegación --}}
        <nav class="flex flex-col gap-2">

            {{-- Home / Dashboard --}}
            <a href="{{ route('home') }}" {{-- MEJORA: Lógica de clases para estado activo/inactivo --}} class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                       @if (!isset($activeView) || $activeView === 'home' || $activeView === 'stats') 
                           bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30 
                       @else 
                           text-gray-300 hover:text-white hover:bg-white/10 
                       @endif">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-medium">Dashboard</span>
            </a>

            {{-- Estadísticas / Líderes --}}
            <a href="{{ route('home') }}?view=stats" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                       @if (isset($activeView) && $activeView === 'stats') 
                           bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30 
                       @else 
                           text-gray-300 hover:text-white hover:bg-white/10 
                       @endif">
                <span class="material-symbols-outlined">leaderboard</span>
                <span class="font-medium">Tops</span>
            </a>

            {{-- 11 IDEAL (NUEVO) --}}
            <a href="{{ route('ideal-eleven') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                       @if (request()->routeIs('ideal-eleven')) 
                           bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30 
                       @else 
                           text-gray-300 hover:text-white hover:bg-white/10 
                       @endif">
                <span class="material-symbols-outlined">stars</span>
                <span class="font-medium">11 Ideal</span>
            </a>

            {{-- MVP JORNADA (NUEVO) --}}
            <a href="{{ route('mvp.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                       @if (request()->routeIs('mvp.index')) 
                           bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30 
                       @else 
                           text-gray-300 hover:text-white hover:bg-white/10 
                       @endif">
                <span class="material-symbols-outlined">military_tech</span>
                <span class="font-medium">MVP Jornada</span>
            </a>


            {{-- CALENDARIO DE PARTIDOS (NUEVO) --}}
            <a href="{{ route('matches.calendar') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                       @if (isset($activeView) && $activeView === 'calendar') 
                           bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30 
                       @else 
                           text-gray-300 hover:text-white hover:bg-white/10 
                       @endif">
                <span class="material-symbols-outlined">calendar_month</span>
                <span class="font-medium">Calendario | Partidos</span>
            </a>

            {{-- NOTICIAS (NUEVO) --}}
            <a href="{{ route('news.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                       @if (request()->routeIs('news.*')) 
                           bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30 
                       @else 
                           text-gray-300 hover:text-white hover:bg-white/10 
                       @endif">
                <span class="material-symbols-outlined">newspaper</span>
                <span class="font-medium">Noticias</span>
            </a>

            {{-- REGLAS DE LA LIGA (NUEVO) --}}
            <a href="{{ route('rules.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                       @if (isset($activeView) && $activeView === 'rules') 
                           bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30 
                       @else 
                           text-gray-300 hover:text-white hover:bg-white/10 
                       @endif">
                <span class="material-symbols-outlined">gavel</span>
                <span class="font-medium">Reglas de la Liga</span>
            </a>

            {{-- GALERÍA --}}
            <a href="{{ route('gallery.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                       @if (request()->routeIs('gallery.*')) 
                           bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30 
                       @else 
                           text-gray-300 hover:text-white hover:bg-white/10 
                       @endif">
                <span class="material-symbols-outlined">photo_library</span>
                <span class="font-medium">Galería</span>
            </a>

            {{-- MINIJUEGO --}}
            <a href="{{ route('games.index') }}" @guest onclick="event.preventDefault(); openLoginModal();" @endguest
                class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors rounded-lg group">
                <span
                    class="material-symbols-outlined mr-3 group-hover:text-green-400 transition-colors">sports_esports</span>
                <span class="font-medium">Minijuegos</span>
            </a>

            {{-- LINEUP BUILDER (NUEVO) --}}
            <a href="{{ route('lineup.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                       @if (request()->routeIs('lineup.*')) 
                           bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30 
                       @else 
                           text-gray-300 hover:text-white hover:bg-white/10 
                       @endif">
                <span class="material-symbols-outlined">tactic</span>
                <span class="font-medium">Pizarra Táctica</span>
            </a>

            <br><br>
            {{-- Panel de Administración --}}
            @if (session('is_admin'))
                <a href="{{ route('admin.panel') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                                                                           @if (isset($activeView) && $activeView === 'admin') 
                                                                               bg-gradient-to-r from-primary to-emerald-600 text-white shadow-lg shadow-primary/30 
                                                                           @else 
                                                                               text-gray-300 hover:text-white hover:bg-white/10 
                                                                           @endif">
                    <span class="material-symbols-outlined">admin_panel_settings</span>
                    <span class="font-medium">Panel Admin</span>
                </a>

                {{-- ANÁLISIS PROFUNDO (NUEVO) --}}
                <a href="{{ route('admin.analysis.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                                                                           @if (request()->routeIs('admin.analysis.*')) 
                                                                               bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-lg shadow-purple-500/30 
                                                                           @else 
                                                                               text-gray-300 hover:text-white hover:bg-white/10 
                                                                           @endif">
                    <span class="material-symbols-outlined">psychology</span>
                    <span class="font-medium">Análisis Profundo</span>
                </a>
            @endif

        </nav>
    </div>
    {{-- Pie de Página / Control de Sesión --}}
    <div>
        @if (session('is_admin'))
            {{-- MEJORA: Hover más reactivo --}}
            <a href="{{ route('logout') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-600/20 text-red-400 hover:bg-red-500/30 hover:text-red-300 transition-all duration-200">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-medium">Cerrar Sesión (Admin)</span>
            </a>
        @else
            {{-- MEJORA: Botón de Login con gradiente y efecto "glow" --}}
            <button onclick="openLoginModal()"
                class="w-full flex items-center justify-center gap-3 px-4 py-3 rounded-lg text-white transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-glow bg-gradient-to-r from-primary to-emerald-600 hover:from-primary/90 hover:to-emerald-600/90">
                <span class="material-symbols-outlined">login</span>
                <span class="font-medium">Iniciar Sesión</span>
            </button>
        @endif
    </div>
</aside>