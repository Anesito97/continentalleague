<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    {{-- Panel Lateral de Navegación Admin --}}
    <nav class="lg:col-span-1 card p-4 space-y-2 hover:transform-none">
        <h3 class="text-xl font-bold text-green-400 mb-4">Panel Admin</h3>
        <div id="admin-user-info" class="text-xs text-gray-500 mb-4 border-b border-gray-700 pb-2">
            ID de Sesión: {{ session('admin_username') }}
        </div>

        {{-- Botones de navegación interna que recargan la página con el parámetro 'activeAdminContent' --}}
        <a href="{{ route('admin.teams') }}"
            class="admin-nav-item w-full text-left p-3 rounded-lg flex items-center text-sm font-medium @if ($activeAdminContent === 'teams') bg-gray-700 @endif">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">...</svg> Gestión de Equipos
        </a>
        <a href="{{ route('admin.players') }}"
            class="admin-nav-item w-full text-left p-3 rounded-lg flex items-center text-sm font-medium @if ($activeAdminContent === 'players') bg-gray-700 @endif">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">...</svg> Gestión de
            Jugadores
        </a>
        <a href="{{ route('admin.matches') }}"
            class="admin-nav-item w-full text-left p-3 rounded-lg flex items-center text-sm font-medium">
            Gestión de Partidos
        </a>
        <a href="{{ route('admin.finalize-match') }}"
            class="admin-nav-item w-full text-left p-3 rounded-lg bg-green-700 hover:bg-green-600 flex items-center text-sm font-bold mt-4 @if ($activeAdminContent === 'finalize-match') bg-green-600 @endif">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">...</svg> FINALIZAR
            PARTIDO
        </a>
    </nav>

    {{-- Área de Contenido Admin --}}
    <div class="lg:col-span-3">
        <h2 class="text-4xl font-bold text-white mb-6 border-b border-green-700 pb-2" id="admin-title">
            {{ match ($activeAdminContent) {
                'teams' => 'Gestión de Equipos',
                'players' => 'Gestión de Jugadores',
                'matches' => 'Gestión de Partidos',
                'finalize-match' => 'Finalizar Partido',
                default => 'Panel de Administración',
            } }}
        </h2>

        {{-- INCLUSIÓN DINÁMICA DE CONTENIDO --}}
        @if ($activeAdminContent === 'teams')
            @include('admin.teams', ['teams' => $teams])
        @elseif($activeAdminContent === 'players')
            @include('admin.players', ['teams' => $teams, 'players' => $players])
        @elseif($activeAdminContent === 'matches')
            @include('admin.matches', ['teams' => $teams, 'pendingMatches' => $pendingMatches])
        @elseif($activeAdminContent === 'finalize-match')
            @include('admin.finalize', ['pendingMatches' => $pendingMatches, 'players' => $players])
        @endif
    </div>
</div>
