<?php
namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use Illuminate\Http\Request;
use App\Models\Noticia;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    // Función central que carga TODOS los datos necesarios
    private function loadAllData()
    {
        // FIX CRÍTICO: Cargar TODOS los jugadores para rankings globales.
        $players = Jugador::with('equipo')->get();

        // Carga de equipos y cálculo de la guía de forma (se mantiene)
        $teams = Equipo::orderByDesc('puntos')->orderByDesc('goles_a_favor')->get();

        // Obtener la jornada activa (la más baja con partidos pendientes)
        $activeJornada = Partido::where('estado', 'pendiente')->min('jornada');

        $pendingMatches = Partido::with(['localTeam', 'visitorTeam'])
            ->where('estado', 'pendiente')
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $recentMatches = Partido::with([
            'localTeam',
            'visitorTeam',
            'eventos.jugador',
            'eventos.equipo'
        ])
            ->orderBy('fecha_hora', 'desc')
            ->limit(10)
            ->get();

        $teams = $teams->map(function ($team) {
            // ... (Lógica de cálculo de form_guide y discipline_points)

            // Calculamos disciplina (requiere la relación jugadores previamente cargada)
            $disciplinePoints = ($team->jugadores->sum('amarillas') * 1) + ($team->jugadores->sum('rojas') * 3);
            $team->discipline_points = $disciplinePoints;

            // Lógica de forma
            $lastMatches = Partido::where('estado', 'finalizado')
                ->where(function ($query) use ($team) {
                    $query->where('equipo_local_id', $team->id)
                        ->orWhere('equipo_visitante_id', $team->id);
                })
                ->orderBy('fecha_hora', 'desc')
                ->take(5)
                ->get();
            $formGuide = '';
            foreach ($lastMatches as $match) {
                $result = 'P';
                if ($match->equipo_local_id === $team->id) {
                    if ($match->goles_local > $match->goles_visitante) {
                        $result = 'G';
                    } elseif ($match->goles_local === $match->goles_visitante) {
                        $result = 'E';
                    }
                } elseif ($match->equipo_visitante_id === $team->id) {
                    if ($match->goles_visitante > $match->goles_local) {
                        $result = 'G';
                    } elseif ($match->goles_visitante === $match->goles_local) {
                        $result = 'E';
                    }
                }
                $formGuide .= $result;
            }
            $team->form_guide = str_pad($formGuide, 5, '-', STR_PAD_RIGHT);

            return $team;
        });

        // ⬇️ CALCULAR TOPS: Ordenar globalmente y filtrar por criterio (>= 1) ⬇️

        // Goleadores: Orden descendente por Goles (solo si tienen > 0)
        $topScorers = $players->filter(fn($p) => $p->goles > 0)->sortByDesc('goles');

        // Asistentes: Orden descendente por Asistencias (solo si tienen > 0)
        $topAssists = $players->filter(fn($p) => $p->asistencias > 0)->sortByDesc('asistencias');

        // Porteros: Orden descendente por Paradas (solo si son porteros y tienen > 0)
        $topKeepers = $players->filter(fn($p) => strtolower($p->posicion) === 'portero' && $p->paradas > 0)->sortByDesc('paradas');

        // MÉTICAS DE DASHBOARD (Aseguramos que operen con colecciones completas)
        $topImpactPlayer = $players->sortByDesc(fn($p) => $p->goles + $p->asistencias)->first();
        $bestDefenseTeam = $teams->sortBy('goles_en_contra')->first();
        $mostOffensiveTeam = $teams->sortByDesc('goles_a_favor')->first();
        $cleanestTeam = $teams->sortBy('discipline_points')->first();

        // Calcular variables generales
        $totalMatches = $teams->sum('partidos_jugados') / 2;
        $totalGoals = $teams->sum('goles_a_favor');
        $avgGoals = ($totalMatches > 0) ? number_format($totalGoals / $totalMatches, 2) : 0;

        $newsItems = \App\Models\Noticia::orderBy('created_at', 'desc')->take(5)->get();

        return compact(
            'teams',
            'players',
            'pendingMatches',
            'recentMatches',
            'topScorers',
            'topAssists',
            'topKeepers',
            'topImpactPlayer',
            'bestDefenseTeam',
            'mostOffensiveTeam',
            'cleanestTeam',
            'totalMatches',
            'totalGoals',
            'avgGoals',
            'newsItems',
            'activeJornada',
        );
    }

    // 1. VISTA PÚBLICA (HOME/STANDINGS/STATS)
    public function index(Request $request) // ⬅️ Aceptar el objeto Request
    {
        $data = $this->loadAllData();

        // ⬇️ Determinar la vista activa (home por defecto) ⬇️
        $data['news'] = $this->getEmptyNewsPaginator();
        $data['activeView'] = $request->query('view', 'home');

        return view('index', $data);
    }

    private function getEmptyNewsPaginator()
    {
        return new \Illuminate\Pagination\LengthAwarePaginator(
            items: new \Illuminate\Support\Collection(),
            total: 0,
            perPage: 10,
            currentPage: 1
        );
    }

    // 2. VISTAS DE ADMINISTRACIÓN (Llama a loadAllData() y añade la vista activa)
    public function adminTeams()
    {
        session(['activeAdminContent' => 'teams']);
        $data = $this->loadAllData();
        // ⬇️ FIX: Inicializar $news para la vista, si no fue cargada ⬇️
        $data['news'] = $this->getEmptyNewsPaginator();
        $data['activeView'] = 'admin';

        return view('index', $data);
    }

    public function adminPlayers()
    {
        session(['activeAdminContent' => 'players']);
        $data = $this->loadAllData();
        $data['news'] = $this->getEmptyNewsPaginator();
        $data['activeView'] = 'admin';

        return view('index', $data);
    }

    public function adminMatches()
    {
        session(['activeAdminContent' => 'matches']);
        $data = $this->loadAllData();
        $data['news'] = $this->getEmptyNewsPaginator();
        $data['activeView'] = 'admin';

        return view('index', $data);
    }

    public function adminFinalizeMatch()
    {
        session(['activeAdminContent' => 'finalize-match']);
        $data = $this->loadAllData();
        $data['news'] = $this->getEmptyNewsPaginator();
        $data['activeView'] = 'admin';

        return view('index', $data);
    }

    public function adminNews()
    {
        // Cargar las noticias paginadas reales
        $news = \App\Models\Noticia::orderBy('publicada_en', 'desc')->paginate(10);

        session(['activeAdminContent' => 'news']);
        $data = $this->loadAllData();

        // Aquí, $data['news'] obtiene los datos reales, NO el paginador vacío.
        $data['news'] = $news;
        $data['activeView'] = 'admin';

        return view('index', $data);
    }

    public function editTeam(Equipo $equipo)
    {
        $data = $this->loadAllData();
        $data['item'] = $equipo;
        $data['type'] = 'team';
        return view('edit', $data); // Usaremos una vista genérica 'edit.blade.php'
    }

    public function editPlayer(Jugador $jugador)
    {
        $data = $this->loadAllData();
        $data['item'] = $jugador;
        $data['type'] = 'player';
        return view('edit', $data);
    }

    public function editMatch(Partido $partido)
    {
        $data = $this->loadAllData();
        $data['item'] = $partido;
        $data['type'] = 'match';
        // Asegúrate de cargar las relaciones necesarias para el formulario de partido
        $partido->load(['localTeam', 'visitorTeam']);
        return view('edit', $data);
    }

    public function editNews(Noticia $noticia)
    {
        // Cargar todos los datos base (equipos, jugadores) necesarios para el layout
        $data = $this->loadAllData();

        // Asignar el ítem actual y su tipo para la vista edit.blade.php
        $data['item'] = $noticia;
        $data['type'] = 'news';

        // Necesitas pasar los equipos si el formulario de edición (edit.blade.php) los requiere
        // $data['teams'] = $data['teams']; 

        return view('edit', $data);
    }

    public function showTeamPlayers(Equipo $equipo)
    {
        // Asegúrate de cargar la relación 'jugadores' y ordenarlos por número de camiseta
        $equipo->load([
            'jugadores' => function ($query) {
                $query->orderBy('numero', 'asc');
            }
        ]);

        $data = $this->loadAllData(); // Carga todos los datos generales (teams, pending matches, etc.)
        $data['equipoActual'] = $equipo;
        $data['players'] = $equipo->jugadores; // Sobrescribimos 'players' con solo los de este equipo
        $data['activeView'] = 'admin';
        $data['activeAdminContent'] = 'teams'; // Mantenemos la navegación activa en 'teams'

        return view('admin.team_players', $data);
    }

    public function showCalendar(Request $request)
    {
        // 1. Determinar el filtro activo
        $filter = $request->query('status', 'pending');

        // ⬇️ Obtener el número de jornada activa si se pasa en la URL, o la jornada más baja pendiente ⬇️
        $activeJornada = $request->query('jornada');

        $query = Partido::with(['localTeam', 'visitorTeam', 'eventos.jugador', 'eventos.equipo'])
            ->orderBy('jornada', 'asc') // ⬅️ Ordenar primero por jornada
            ->orderBy('fecha_hora', 'asc');

        // 2. Aplicar filtros de ESTADO (pending, finished, all)
        if ($filter === 'pending') {
            $query->where('estado', 'pendiente');
        } elseif ($filter === 'finished') {
            $query->where('estado', 'finalizado');
        }

        // 3. Aplicar filtro de JORNADA si se selecciona una específica
        if ($activeJornada) {
            $query->where('jornada', $activeJornada);
        }

        $allMatches = $query->get();

        // 4. Agrupar los partidos por JORNADA (CRÍTICO)
        // Ya no agrupamos por fecha, sino por el número de jornada.
        $matchesByJornada = $allMatches->groupBy('jornada');

        // 5. Determinar todas las jornadas existentes para el selector
        $allJornadas = Partido::select('jornada')->distinct()->orderBy('jornada', 'asc')->pluck('jornada');
        // Determinar la jornada más baja que contiene partidos pendientes (para el foco inicial)
        $firstPendingJornada = Partido::where('estado', 'pendiente')->min('jornada');

        // Si no hay jornada activa en la URL, usar la primera pendiente
        if (!$activeJornada && $firstPendingJornada) {
            $activeJornada = $firstPendingJornada;
        } elseif (!$activeJornada) {
            $activeJornada = $allJornadas->first(); // Si no hay pendientes, usar la primera jornada existente
        }


        $data = $this->loadAllData();
        $data['matchesByJornada'] = $matchesByJornada; // ⬅️ Nuevo nombre
        $data['activeView'] = 'calendar';
        $data['activeFilter'] = $filter;
        $data['allJornadas'] = $allJornadas; // Para el selector
        $data['activeJornada'] = (int) $activeJornada; // Para saber qué pestaña resaltar

        return view('calendar', $data);
    }

    public function showRules()
    {
        // Carga solo los datos necesarios para el layout (si no se cargan globalmente)
        $data = $this->loadAllData();
        $data['activeView'] = 'rules'; // Nueva vista activa

        return view('rules', $data); // Usaremos una nueva vista 'rules.blade.php'
    }
}