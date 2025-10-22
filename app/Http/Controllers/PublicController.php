<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Noticia;
use App\Models\Equipo; // Necesario para loadAllData si lo mueves aquí
use App\Models\Jugador;
use App\Models\GalleryItem;
use Illuminate\Support\Collection;

class PublicController extends Controller
{
    // Método para listar todas las noticias con paginación
    public function indexNews()
    {
        // Obtener las noticias, ordenadas por fecha de publicación, 10 por página
        $news = Noticia::orderBy('publicada_en', 'desc')->paginate(10);

        // Puedes llamar a loadAllData() si necesitas los tops/teams/etc. en el layout,
        // pero aquí solo pasaremos lo esencial para la vista de noticias.
        $teams = Equipo::all(); // Carga de datos para el layout si es necesario

        return view('news.index', compact('news', 'teams'));
    }

    // Método para mostrar el detalle de una sola noticia
    public function showNews(Noticia $noticia)
    {
        $teams = Equipo::all(); // Carga de datos para el layout

        return view('news.show', compact('noticia', 'teams'));
    }

    public function showTeamProfile(Equipo $equipo)
    {
        // 1. Cargar relaciones CRÍTICAS
        $equipo->load([
            'jugadores' => fn($query) => $query->orderBy('numero', 'asc'),
            'localMatches' => fn($query) => $query->where('estado', 'finalizado')->orderBy('fecha_hora', 'desc'),
            'visitorMatches' => fn($query) => $query->where('estado', 'finalizado')->orderBy('fecha_hora', 'desc'),
        ]);

        // 2. Cálculo de métricas avanzadas
        $totalPlayers = $equipo->jugadores->count();
        $totalGoles = $equipo->goles_a_favor; // Usamos el dato de la tabla equipos
        $totalAsistencias = $equipo->jugadores->sum('asistencias');
        $pj = $equipo->partidos_jugados;

        // --- MÉTRICAS GENERALES ---
        $goalDifference = $equipo->goles_a_favor - $equipo->goles_en_contra;
        $winRatio = $pj > 0 ? number_format(($equipo->ganados / $pj) * 100, 1) : 0;
        $gpj = $pj > 0 ? number_format($totalGoles / $pj, 2) : 0;
        $gcpj = $pj > 0 ? number_format($equipo->goles_en_contra / $pj, 2) : 0;
        $offensiveEfficiency = ($totalGoles + $equipo->goles_en_contra) > 0
            ? number_format(($totalGoles / ($totalGoles + $equipo->goles_en_contra)) * 100, 1) : 0;

        // --- MÉTRICAS DE DISCIPLINA Y RECURSOS ---
        $totalYellows = $equipo->jugadores->sum('amarillas');
        $totalReds = $equipo->jugadores->sum('rojas');
        $totalCards = $totalYellows + $totalReds;
        $cardsRatio = $pj > 0 ? number_format($totalCards / $pj, 2) : 0; // Tarjetas por Partido
        $totalParadas = $equipo->jugadores->sum('paradas');

        // --- ANALISIS DE FORTALEZA LOCAL VS VISITANTE ---
        $localStats = ['PJ' => 0, 'Ptos' => 0, 'GF' => 0, 'GC' => 0, 'W_Ratio' => 0];
        $visitorStats = ['PJ' => 0, 'Ptos' => 0, 'GF' => 0, 'GC' => 0, 'W_Ratio' => 0];

        // Se deben iterar los partidos para calcular estas stats específicas (más complejo)
        // Por simplicidad, aquí solo pasamos los datos para la vista.

        // --- ANALISIS POSICIONAL Y OFENSIVO ---
        $jugadoresPorPos = $equipo->jugadores->groupBy('posicion');
        $delanteros = $jugadoresPorPos->get('delantero', new Collection());
        $golesDelanteros = $delanteros->sum('goles');

        $golesNoDelanteros = $totalGoles - $golesDelanteros;

        $golesDelanterosRatio = $totalGoles > 0
            ? number_format(($golesDelanteros / $totalGoles) * 100, 1)
            : 0;

        // --- TOP 3 JUGADORES CON MAYOR DISCIPLINA (Menos Tarjetas) ---
        $playersWithDiscipline = $equipo->jugadores->map(function ($p) {
            $p->discipline_score = ($p->rojas * 3) + ($p->amarillas * 1);
            return $p;
        });
        // Jugadores con menos de 3 puntos de disciplina (para destacar el fair play)
        $topCleanPlayers = $playersWithDiscipline->where('discipline_score', '<', 3)->sortBy('discipline_score')->take(3);


        // --- Racha y Goleadores (Existente) ---
        $allPlayedMatches = $equipo->localMatches->merge($equipo->visitorMatches)
            ->sortByDesc('fecha_hora');
        $streak = $this->calculateStreak($equipo, $allPlayedMatches->take(5));
        $topScorer = $equipo->jugadores->sortByDesc('goles')->first();
        $topAssist = $equipo->jugadores->sortByDesc('asistencias')->first();

        $allTeamsRanked = Equipo::orderByDesc('puntos')
            ->orderByDesc('goles_a_favor')
            ->get();

        $leaguePosition = $allTeamsRanked->search(function ($item) use ($equipo) {
            return $item->id === $equipo->id;
        });

        // La posición es el índice + 1 (ya que el índice empieza en 0)
        $leaguePosition = $leaguePosition !== false ? $leaguePosition + 1 : 'N/A';

        // 3. Preparar los datos para la vista
        $data = [
            'equipo' => $equipo,
            'totalPlayers' => $totalPlayers,
            'totalGoles' => $totalGoles,
            'totalAsistencias' => $totalAsistencias,
            'topScorer' => $topScorer,
            'recentHistory' => $allPlayedMatches->take(5), // Solo 5 para el historial
            'activeView' => 'team_profile',
            'goalDifference' => $goalDifference,
            'winRatio' => $winRatio,
            'gpj' => $gpj,
            'streak' => $streak,
            'offensiveEfficiency' => $offensiveEfficiency,
            'gcpj' => $gcpj,
            'totalCards' => $totalCards,
            'totalYellows' => $totalYellows,
            'totalReds' => $totalReds,
            'topAssist' => $topAssist,
            'cardsRatio' => $cardsRatio,
            'totalParadas' => $totalParadas,
            'topCleanPlayers' => $topCleanPlayers, // Top 3 de Fair Play
            'golesDelanterosRatio' => $golesDelanterosRatio,
            'golesNoDelanteros' => $golesNoDelanteros,
            'jugadoresPorPos' => $jugadoresPorPos,
            'leaguePosition' => $leaguePosition,
        ];

        return view('team_profile', $data);
    }

    // Función auxiliar para calcular la racha (W, L, D)
    private function calculateStreak(Equipo $equipo, Collection $matches)
    {
        $streak = '';
        foreach ($matches as $match) {
            $isLocal = $match->equipo_local_id === $equipo->id;

            if ($match->goles_local === $match->goles_visitante) {
                $result = 'E';
            } elseif (($isLocal && $match->goles_local > $match->goles_visitante) || (!$isLocal && $match->goles_visitante > $match->goles_local)) {
                $result = 'G';
            } else {
                $result = 'P';
            }
            $streak .= $result;
        }
        return strrev($streak); // Retorna la racha en orden cronológico inverso (el último jugado es la primera letra)
    }

    public function showPlayerProfile(Jugador $jugador)
    {
        // 1. Cargar relaciones CRÍTICAS: Equipo y Eventos
        $jugador->load([
            'equipo',
            'eventos' => fn($query) => $query->with('partido')->orderBy('minuto', 'asc'),
        ]);

        // 2. CÁLCULO DE MÉTRICAS INDIVIDUALES AVANZADAS
        $pj = $jugador->equipo->partidos_jugados ?? 0;
        $equipoTotalGoles = $jugador->equipo->goles_a_favor ?? 1; // Usar 1 para evitar división por cero

        // A. Puntuación de Impacto (MVP Score - Existente)
        $mvpScore = ($jugador->goles * 3) + ($jugador->asistencias * 2) + ($jugador->paradas * 0.5) - ($jugador->rojas * 3) - ($jugador->amarillas * 1);

        // B. Ratio G/PJ y A/PJ (Existente)
        $gpjRatio = $pj > 0 ? number_format($jugador->goles / $pj, 2) : 0;
        $apjRatio = $pj > 0 ? number_format($jugador->asistencias / $pj, 2) : 0;

        // C. Tarjetas Totales (Existente)
        $disciplineScore = ($jugador->rojas * 3) + ($jugador->amarillas * 1);

        // D. Eficiencia de Portero (Existente)
        $keeperEfficiency = 0;
        if (strtolower($jugador->posicion_especifica) === 'portero' && $pj > 0) {
            $golesEnContra = $jugador->equipo->goles_en_contra ?? 0;
            $keeperEfficiency = number_format(($jugador->paradas / ($golesEnContra + $jugador->paradas)) * 100, 1);
        }

        // E. Historial de Eventos (Existente)
        $recentEvents = $jugador->eventos->sortByDesc(fn($e) => $e->partido->fecha_hora)->take(10);


        // ⬇️ 1. NUEVA MÉTRICA: Participación en Goles (G+A / Total Goles del Equipo) ⬇️
        $totalGoalContributions = $jugador->goles + $jugador->asistencias;
        $participationRate = $equipoTotalGoles > 0
            ? number_format(($totalGoalContributions / $equipoTotalGoles) * 100, 1)
            : 0;

        // ⬇️ 2. NUEVA MÉTRICA: Contribución Promedio por Evento (Asumiendo 10 disparos simulados) ⬇️
        // Usaremos esta métrica como un indicador de 'calidad del toque'
        $totalActions = $jugador->goles + $jugador->asistencias + ($jugador->paradas > 0 ? $jugador->paradas : 0);
        $contributionPerMatch = $pj > 0 ? number_format($totalActions / $pj, 2) : 0;

        // ⬇️ 3. NUEVA MÉTRICA: Porcentaje de Victorias cuando el jugador participa ⬇️
        // Esto es un indicador avanzado y solo se puede simular parcialmente con los datos que tenemos.
        // Simularemos la 'Tasa de Victorias en PJ' (Victorias Equipo / PJ Equipo)
        $winRateWithPlayer = $pj > 0
            ? number_format(($jugador->equipo->ganados / $pj) * 100, 1)
            : 0;

        $goalRecords = [
            'dobletes' => 0,    // 2 goles
            'hat_tricks' => 0,  // 3 goles
            'poker' => 0,       // 4 goles
            'manita' => 0,      // 5 goles
            'mas_cinco' => 0    // > 5 goles
        ];

        // 1. Agrupar los eventos por Partido ID
        $goalsByMatch = $jugador->eventos
            ->where('tipo_evento', 'gol')
            ->groupBy('partido_id');

        // 2. Contar los goles en cada partido
        foreach ($goalsByMatch as $matchId => $goals) {
            $goalCount = $goals->count();

            if ($goalCount === 2) {
                $goalRecords['dobletes']++;
            } elseif ($goalCount === 3) {
                $goalRecords['hat_tricks']++;
            } elseif ($goalCount === 4) {
                $goalRecords['poker']++;
            } elseif ($goalCount === 5) {
                $goalRecords['manita']++;
            } elseif ($goalCount > 5) {
                $goalRecords['mas_cinco']++;
            }
        }


        // 3. Preparar los datos para la vista
        $data = [
            'jugador' => $jugador,
            'equipo' => $jugador->equipo,
            'activeView' => 'player_profile',

            // MÉTRICAS AVANZADAS
            'mvpScore' => $mvpScore,
            'gpjRatio' => $gpjRatio,
            'apjRatio' => $apjRatio,
            'disciplineScore' => $disciplineScore,
            'keeperEfficiency' => $keeperEfficiency,
            'goalRecords' => $goalRecords,

            // ⬇️ NUEVOS DATOS AGREGADOS ⬇️
            'participationRate' => $participationRate,
            'contributionPerMatch' => $contributionPerMatch,
            'winRateWithPlayer' => $winRateWithPlayer,

            // Historial
            'recentEvents' => $recentEvents,
            'pj' => $pj,
        ];

        return view('player_profile', $data);
    }

    public function showGallery()
    {
        // Cargar ítems de la galería, ordenados por fecha, paginados a 15 por página
        $galleryItems = GalleryItem::with('match.localTeam', 'match.visitorTeam')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Carga de datos para el layout
        $teams = Equipo::all();

        return view('gallery.index', compact('galleryItems', 'teams')); // Nueva vista: gallery/index.blade.php
    }
}