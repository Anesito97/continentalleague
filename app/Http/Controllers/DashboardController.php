<?php
namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use Illuminate\Http\Request;
use App\Models\Noticia;
use App\Http\Controllers\VoteController;

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

        $nextMatch = $pendingMatches->first();
        $prediction = ['localProb' => 34, 'drawProb' => 33, 'visitorProb' => 33, 'title' => 'Probabilidad (Estimada)'];
        $h2hRecord = ['G' => 0, 'E' => 0, 'P' => 0, 'total' => 0]; // Inicializamos por si no hay partido

        if ($nextMatch) {
            // ⬇️ NUEVO CÁLCULO DE PREDICCIÓN COMBINADO ⬇️
            $prediction = $this->getMatchPrediction($nextMatch->localTeam, $nextMatch->visitorTeam);

            // También obtenemos el H2H para mostrarlo en la vista, aunque ya se usa dentro de la predicción
            $h2hRecord = $this->calculateH2H($nextMatch->localTeam, $nextMatch->visitorTeam);
        }

        $communityVotes = VoteController::getVotes($nextMatch->id ?? null);
        $communityTotal = array_sum($communityVotes);

        $communityLocalProb = $communityTotal > 0 ? number_format(($communityVotes['local'] / $communityTotal) * 100, 0) : 33;
        $communityVisitorProb = $communityTotal > 0 ? number_format(($communityVotes['visitor'] / $communityTotal) * 100, 0) : 33;
        $communityDrawProb = 100 - $communityLocalProb - $communityVisitorProb; // El restante

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
            'nextMatch',
            'h2hRecord',
            'prediction',
            'communityLocalProb',
            'communityVisitorProb',
            'communityDrawProb',
            'communityVotes',
            'communityTotal'
        );
    }

    private function calculateH2H(Equipo $team1, Equipo $team2)
    {
        // Buscar todos los partidos finalizados entre estos dos equipos
        $h2hMatches = Partido::where('estado', 'finalizado')
            ->where(function ($query) use ($team1, $team2) {
                $query->where(function ($q) use ($team1, $team2) {
                    $q->where('equipo_local_id', $team1->id)->where('equipo_visitante_id', $team2->id);
                })->orWhere(function ($q) use ($team1, $team2) {
                    $q->where('equipo_local_id', $team2->id)->where('equipo_visitante_id', $team1->id);
                });
            })
            ->orderBy('fecha_hora', 'desc')
            ->get();

        $record = ['G' => 0, 'E' => 0, 'P' => 0, 'total' => $h2hMatches->count()];

        foreach ($h2hMatches as $match) {
            $team1IsLocal = $match->equipo_local_id === $team1->id;
            $team1Score = $team1IsLocal ? $match->goles_local : $match->goles_visitante;
            $team2Score = $team1IsLocal ? $match->goles_visitante : $match->goles_local;

            if ($team1Score > $team2Score) {
                $record['G']++;
            } elseif ($team1Score < $team2Score) {
                $record['P']++;
            } else {
                $record['E']++;
            }
        }

        return $record;
    }

    private function getRecentForm(Equipo $team, int $limit = 3)
    {
        $recentMatches = Partido::where('estado', 'finalizado')
            ->where(function ($query) use ($team) {
                $query->where('equipo_local_id', $team->id)
                    ->orWhere('equipo_visitante_id', $team->id);
            })
            ->orderBy('fecha_hora', 'desc')
            ->take($limit)
            ->get();

        $form = ['G' => 0, 'E' => 0, 'P' => 0, 'total' => $recentMatches->count()];

        foreach ($recentMatches as $match) {
            $isLocal = $match->equipo_local_id === $team->id;
            $teamScore = $isLocal ? $match->goles_local : $match->goles_visitante;
            $opponentScore = $isLocal ? $match->goles_visitante : $match->goles_local;

            if ($teamScore > $opponentScore) {
                $form['G']++;
            } elseif ($teamScore < $opponentScore) {
                $form['P']++;
            } else {
                $form['E']++;
            }
        }

        return $form;
    }

    private function getMatchPrediction(Equipo $localTeam, Equipo $visitorTeam)
    {
        // --- 1. Definir Pesos ---
        $h2hWeight = 0.6;  // 60% de importancia para el H2H
        $formWeight = 0.4; // 40% de importancia para la racha

        // --- 2. Obtener Datos ---
        $h2hRecord = $this->calculateH2H($localTeam, $visitorTeam);
        $localForm = $this->getRecentForm($localTeam, 3);
        $visitorForm = $this->getRecentForm($visitorTeam, 3);

        // Si no hay datos de ningún tipo, devolvemos una estimación base.
        if ($h2hRecord['total'] == 0 && $localForm['total'] == 0 && $visitorForm['total'] == 0) {
            return [
                'localProb' => 34,
                'drawProb' => 33,
                'visitorProb' => 33,
                'title' => 'Probabilidad (Estimada)'
            ];
        }

        // --- 3. Calcular "Puntuación de Poder" para cada factor ---
        // Puntuación de 0 a 1 (0 = peor, 1 = mejor)
        $h2hPoints = ($h2hRecord['G'] * 3) + ($h2hRecord['E'] * 1);
        $h2hTotalPoints = $h2hRecord['total'] * 3;
        $h2hScore = $h2hTotalPoints > 0 ? $h2hPoints / $h2hTotalPoints : 0.5; // 0.5 es neutral si no hay H2H

        $localFormPoints = ($localForm['G'] * 3) + ($localForm['E'] * 1);
        $localFormTotalPoints = $localForm['total'] * 3;
        $localFormScore = $localFormTotalPoints > 0 ? $localFormPoints / $localFormTotalPoints : 0.5;

        $visitorFormPoints = ($visitorForm['G'] * 3) + ($visitorForm['E'] * 1);
        $visitorFormTotalPoints = $visitorForm['total'] * 3;
        $visitorFormScore = $visitorFormTotalPoints > 0 ? $visitorFormPoints / $visitorFormTotalPoints : 0.5;

        // --- 4. Combinar puntuaciones para obtener el "Power Score" final ---
        // El H2H es relativo, la racha es absoluta. Combinamos para obtener el poder de cada equipo.
        $localPower = ($h2hScore * $h2hWeight) + ($localFormScore * $formWeight);
        // Para el visitante, su "H2H Score" es el inverso del local.
        $visitorPower = ((1 - $h2hScore) * $h2hWeight) + ($visitorFormScore * $formWeight);

        // --- 5. Convertir Power Scores a Probabilidades (Local, Visitante, Empate) ---
        $totalPower = $localPower + $visitorPower;

        // Si el poder total es cero, evitamos división por cero
        if ($totalPower == 0)
            $totalPower = 1;

        // Distribuimos un 75% de la probabilidad entre victoria local y visitante
        $winProbPool = 0.75;
        $localProb = ($localPower / $totalPower) * $winProbPool;
        $visitorProb = ($visitorPower / $totalPower) * $winProbPool;

        // El 25% restante es para el empate
        $drawProb = 1 - ($localProb + $visitorProb);

        return [
            'localProb' => round($localProb * 100),
            'drawProb' => round($drawProb * 100),
            'visitorProb' => round($visitorProb * 100),
            'title' => 'Probabilidad (H2H + Racha)'
        ];
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