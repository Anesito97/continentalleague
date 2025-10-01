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
        $players = Jugador::with('equipo')->get();

        $pendingMatches = Partido::with(['localTeam', 'visitorTeam'])
            ->where('estado', 'pendiente')
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $recentMatches = Partido::with(['localTeam', 'visitorTeam'])
            ->orderBy('fecha_hora', 'desc')
            ->limit(10)
            ->get();

        // ⬇️ CALCULAR TOPS AQUI PARA QUE ESTÉN DISPONIBLES EN TODAS PARTES ⬇️
        $topScorers = $players->sortByDesc('goles')->take(5);
        $topAssists = $players->sortByDesc('asistencias')->take(5);
        $topKeepers = $players->filter(fn($p) => strtolower($p->posicion) === 'portero')->sortByDesc('paradas')->take(5);


        return compact('teams', 'players', 'pendingMatches', 'recentMatches', 'topScorers', 'topAssists', 'topKeepers');
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
}