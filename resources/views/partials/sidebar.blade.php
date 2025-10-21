<aside id="admin-sidebar"
    class="w-3/4 sm:w-72 bg-card-bg p-6 flex-col justify-between border-r border-white/5 shadow-2xl 
              {{-- ⬇️ CLASES DE VISIBILIDAD CRÍTICAS ⬇️ --}}
              fixed top-0 left-0 h-full z-30 sm:relative sm:flex hidden sm:flex">
    <div>
        {{-- Título y Logo --}}
        <div class="flex items-center gap-3 mb-8">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="w-10 h-10 object-contain">
            <h1 class="text-2xl font-bold">Continental League</h1>
        </div>

        {{-- Menú de Navegación --}}
        <nav class="flex flex-col gap-2">

            {{-- Home / Dashboard --}}
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:bg-white/5 
                       @if (!isset($activeView) || $activeView === 'home' || $activeView === 'stats') bg-primary/20 text-primary @endif">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-medium">Dashboard</span>
            </a>

            {{-- Estadísticas / Líderes --}}
            <a href="{{ route('home') }}?view=stats"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:bg-white/5 
                       @if (isset($activeView) && $activeView === 'stats') bg-primary/20 text-primary @endif">
                <span class="material-symbols-outlined">leaderboard</span>
                <span class="font-medium">Tops</span>
            </a>


            {{-- CALENDARIO DE PARTIDOS (NUEVO) --}}
            <a href="{{ route('matches.calendar') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:bg-white/5 
                       @if (isset($activeView) && $activeView === 'calendar') bg-primary/20 text-primary @endif">
                <span class="material-symbols-outlined">calendar_month</span>
                <span class="font-medium">Calendario | Partidos</span>
            </a>

            {{-- NOTICIAS (NUEVO) --}}
            <a href="{{ route('news.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:bg-white/5 
                       @if (request()->routeIs('news.*')) bg-primary/20 text-primary @endif">
                <span class="material-symbols-outlined">newspaper</span>
                <span class="font-medium">Noticias</span>
            </a>

            {{-- REGLAS DE LA LIGA (NUEVO) --}}
            <a href="{{ route('rules.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:bg-white/5 
                       @if (isset($activeView) && $activeView === 'rules') bg-primary/20 text-primary @endif">
                <span class="material-symbols-outlined">gavel</span>
                <span class="font-medium">Reglas de la Liga</span>
            </a>

            {{-- GALERÍA --}}
            <a href="{{ route('gallery.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:bg-white/5 
                       @if (request()->routeIs('gallery.*')) bg-primary/20 text-primary @endif">
                <span class="material-symbols-outlined">photo_library</span>
                <span class="font-medium">Galería</span>
            </a>

            <br><br>
            {{-- Panel de Administración --}}
            @if (session('is_admin'))
                <a href="{{ route('admin.panel') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/70 hover:bg-white/5 
                           @if (isset($activeView) && $activeView === 'admin') bg-primary/20 text-primary @endif">
                    <span class="material-symbols-outlined">admin_panel_settings</span>
                    <span class="font-medium">Panel Admin</span>
                </a>
            @endif

        </nav>
    </div>
    {{-- Pie de Página / Control de Sesión --}}
    <div>
        @if (session('is_admin'))
            <a href="{{ route('logout') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-600/20 text-red-400 hover:bg-red-600/30">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-medium">Cerrar Sesión (Admin)</span>
            </a>
        @else
            <button onclick="document.getElementById('login-modal').classList.remove('hidden')"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-primary text-white hover:bg-primary/90 transition duration-150">
                <span class="material-symbols-outlined">login</span>
                <span class="font-medium">Iniciar Sesión</span>
            </button>
        @endif
    </div>
</aside>
