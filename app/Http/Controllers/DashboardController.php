<?php
namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Función central que carga TODOS los datos necesarios
private function loadAllData()
    {
        // FIX CRÍTICO: Cargar TODOS los jugadores para rankings globales.
        $players = Jugador::with('equipo')->get();
        
        // Carga de equipos y cálculo de la guía de forma (se mantiene)
        $teams = Equipo::orderByDesc('puntos')->orderByDesc('goles_a_favor')->get();

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
                    if ($match->goles_local > $match->goles_visitante) { $result = 'G'; } 
                    elseif ($match->goles_local === $match->goles_visitante) { $result = 'E'; }
                } elseif ($match->equipo_visitante_id === $team->id) {
                    if ($match->goles_visitante > $match->goles_local) { $result = 'G'; } 
                    elseif ($match->goles_visitante === $match->goles_local) { $result = 'E'; }
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
        

        return compact(
            'teams', 'players', 'pendingMatches', 'recentMatches', 
            'topScorers', 'topAssists', 'topKeepers', 
            'topImpactPlayer', 'bestDefenseTeam', 'mostOffensiveTeam', 'cleanestTeam',
            'totalMatches', 'totalGoals', 'avgGoals'
        );
    }

    // 1. VISTA PÚBLICA (HOME/STANDINGS/STATS)
    public function index(Request $request) // ⬅️ Aceptar el objeto Request
    {
        $data = $this->loadAllData();

        // ⬇️ Determinar la vista activa (home por defecto) ⬇️
        $data['activeView'] = $request->query('view', 'home');

        return view('index', $data);
    }

    // 2. VISTAS DE ADMINISTRACIÓN (Llama a loadAllData() y añade la vista activa)
    public function adminTeams()
    {
        session(['activeAdminContent' => 'teams']); // ⬅️ Guardar en sesión
        return $this->adminPanel();
    }
    public function adminPlayers()
    {
        session(['activeAdminContent' => 'players']); // ⬅️ Guardar en sesión
        return $this->adminPanel();
    }
    public function adminMatches()
    {
        session(['activeAdminContent' => 'matches']); // ⬅️ Guardar en sesión
        return $this->adminPanel();
    }
    public function adminFinalizeMatch()
    {
        session(['activeAdminContent' => 'finalize-match']); // ⬅️ Guardar en sesión
        return $this->adminPanel();
    }

    // Y en adminPanel(), aseguramos que se recupere:
    public function adminPanel()
    {
        $data = $this->loadAllData();
        $data['activeView'] = 'admin'; // ⬅️ Fuerza la vista a 'admin'
        // ... (el resto del código adminPanel)

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

    public function showCalendar(Request $request) // ⬅️ Aceptar Request
    {
        // 1. Determinar el filtro activo
        $filter = $request->query('status', 'pending'); // 'pending' es el valor por defecto

        // 2. Cargar todos los partidos y luego filtrar o cargar la colección directamente
        $query = Partido::with(['localTeam', 'visitorTeam'])
            ->orderBy('fecha_hora', 'asc');

        if ($filter === 'pending') {
            $query->where('estado', 'pendiente');
        } elseif ($filter === 'finished') {
            $query->where('estado', 'finalizado')->orderBy('fecha_hora', 'desc'); // Recientes primero
        }
        // Si $filter es 'all' o cualquier otro valor, se cargan todos.

        $allMatches = $query->get();

        // 3. Agrupar los partidos por fecha
        $matchesByDate = $allMatches->groupBy(function ($match) {
            return \Carbon\Carbon::parse($match->fecha_hora)->format('Y-m-d');
        });

        $data = $this->loadAllData();
        $data['matchesByDate'] = $matchesByDate;
        $data['activeView'] = 'calendar';
        $data['activeFilter'] = $filter; // ⬅️ Pasar el filtro activo a la vista

        return view('calendar', $data);
    }
}