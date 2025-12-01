<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

    {{-- Panel Lateral de Navegación Admin (Sidebar) --}}
    <nav class="col-span-1 card p-4 space-y-2 hover:transform-none">
        <h3 class="text-xl font-bold text-green-400 mb-4">Panel Admin</h3>
        <div id="admin-user-info" class="text-xs text-gray-500 mb-4 border-b border-gray-700 pb-2">
            ID de Sesión: {{ session('admin_username') }}
        </div>

        {{-- Gestión de Equipos --}}
        <a href="{{ route('admin.teams') }}" class="admin-nav-item w-full text-left p-3 rounded-lg flex items-center text-sm font-medium transition duration-300
                   @if ($activeAdminContent === 'teams') bg-gray-700 @else hover:bg-gray-700 @endif">
            <span class="material-symbols-outlined mr-2">group</span> Gestión de Equipos
        </a>

        {{-- Gestión de Jugadores --}}
        <a href="{{ route('admin.players') }}" class="admin-nav-item w-full text-left p-3 rounded-lg flex items-center text-sm font-medium transition duration-300
                   @if ($activeAdminContent === 'players') bg-gray-700 @else hover:bg-gray-700 @endif">
            <span class="material-symbols-outlined mr-2">person</span> Gestión de Jugadores
        </a>

        {{-- Gestión de Partidos --}}
        <a href="{{ route('admin.matches') }}" class="admin-nav-item w-full text-left p-3 rounded-lg flex items-center text-sm font-medium transition duration-300
                   @if ($activeAdminContent === 'matches') bg-gray-700 @else hover:bg-gray-700 @endif">
            <span class="material-symbols-outlined mr-2">sports_soccer</span> Gestión de Partidos
        </a>

        {{-- GESTIÓN DE NOTICIAS (NUEVO ENLACE) --}}
        <a href="{{ route('admin.news') }}" class="admin-nav-item w-full text-left p-3 rounded-lg flex items-center text-sm font-medium transition duration-300
                   @if ($activeAdminContent === 'news') bg-gray-700 @else hover:bg-gray-700 @endif">
            <span class="material-symbols-outlined mr-2">newspaper</span> Gestión de Noticias
        </a>

        {{-- GESTIÓN DE NOTIFICACIONES --}}
        <a href="{{ route('admin.notifications.index') }}" class="admin-nav-item w-full text-left p-3 rounded-lg flex items-center text-sm font-medium transition duration-300
                   @if ($activeAdminContent === 'notifications') bg-gray-700 @else hover:bg-gray-700 @endif">
            <span class="material-symbols-outlined mr-2">campaign</span> Notificaciones
        </a>

        {{-- ANALÍTICAS --}}
        <a href="{{ route('admin.analytics') }}" class="admin-nav-item w-full text-left p-3 rounded-lg flex items-center text-sm font-medium transition duration-300
                   @if ($activeAdminContent === 'analytics') bg-gray-700 @else hover:bg-gray-700 @endif">
            <span class="material-symbols-outlined mr-2">query_stats</span> Analíticas
        </a>

        {{-- FINALIZAR PARTIDO --}}
        <a href="{{ route('admin.finalize-match') }}"
            class="admin-nav-item w-full text-left p-3 rounded-lg flex items-center text-sm font-bold mt-4 transition duration-300
                   @if ($activeAdminContent === 'finalize-match') bg-green-600 hover:bg-green-700 @else bg-gray-700 hover:bg-green-700 @endif">
            <span class="material-symbols-outlined mr-2">check_box</span> FINALIZAR PARTIDO
        </a>
    </nav>

    {{-- Área de Contenido Admin --}}
    <div class="col-span-1 lg:col-span-3">
        <h2 class="text-4xl font-bold text-white mb-6 border-b border-green-700 pb-2" id="admin-title">
            {{ match ($activeAdminContent) {
    'news' => 'Gestión de Noticias',
    'teams' => 'Gestión de Equipos',
    'players' => 'Gestión de Jugadores',
    'matches' => 'Gestión de Partidos',
    'notifications' => 'Notificaciones Predefinidas',
    'analytics' => 'Analíticas de Visitas',
    'finalize-match' => 'Finalizar Partido',
    default => 'Panel de Administración',
} }}
        </h2>

        {{-- INCLUSIÓN DINÁMICA DE CONTENIDO --}}
        @if ($activeAdminContent === 'news')
            @include('admin.news', ['news' => $news])
        @elseif ($activeAdminContent === 'teams')
            @include('admin.teams', ['teams' => $teams])
        @elseif($activeAdminContent === 'players')
            @include('admin.players', ['teams' => $teams, 'players' => $players])
        @elseif($activeAdminContent === 'matches')
            @include('admin.matches', ['teams' => $teams, 'pendingMatches' => $pendingMatches])
        @elseif($activeAdminContent === 'finalize-match')
            @include('admin.finalize', ['pendingMatches' => $pendingMatches, 'players' => $players])
        @elseif($activeAdminContent === 'notifications')
            @include('admin.notifications', ['messages' => $messages ?? collect()])
        @elseif($activeAdminContent === 'analytics')
            @include('admin.analytics', ['pageViews' => $pageViews ?? collect()])
        @endif
    </div>
</div>