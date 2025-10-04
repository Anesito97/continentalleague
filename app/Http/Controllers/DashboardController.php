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
        $teams = Equipo::orderByDesc('puntos')->orderByDesc('goles_a_favor')->get();
        $players = Jugador::with('equipo')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

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
            // Obtener los últimos 5 partidos finalizados donde el equipo fue local O visitante
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
                $result = 'P'; // Derrota por defecto

                // Si es el equipo local
                if ($match->equipo_local_id === $team->id) {
                    if ($match->goles_local > $match->goles_visitante) {
                        $result = 'G'; // Ganado
                    } elseif ($match->goles_local === $match->goles_visitante) {
                        $result = 'E'; // Empate
                    }
                    // Si es el equipo visitante
                } elseif ($match->equipo_visitante_id === $team->id) {
                    if ($match->goles_visitante > $match->goles_local) {
                        $result = 'G'; // Ganado
                    } elseif ($match->goles_visitante === $match->goles_local) {
                        $result = 'E'; // Empate
                    }
                }

                $formGuide .= $result;
            }

            // Rellenar con '-' si hay menos de 5 partidos
            $team->form_guide = str_pad($formGuide, 5, '-', STR_PAD_RIGHT);

            return $team;
        });

        // ⬇️ CALCULAR TOPS AQUI PARA QUE ESTÉN DISPONIBLES EN TODAS PARTES ⬇️
        $topScorers = $players->sortByDesc('goles')->take(5);
        $topAssists = $players->sortByDesc('asistencias')->take(5);
        $topKeepers = $players->filter(fn($p) => strtolower($p->posicion) === 'portero')->sortByDesc('paradas')->take(5);

        $bestDefenseTeam = $teams->sortBy('goles_en_contra')->first();
        $mostOffensiveTeam = $teams->sortByDesc('goles_a_favor')->first();

        return compact('teams', 'players', 'pendingMatches', 'recentMatches', 'topScorers', 'topAssists', 'topKeepers', 'bestDefenseTeam', 'mostOffensiveTeam');
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