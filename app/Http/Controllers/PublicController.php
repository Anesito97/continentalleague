<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Noticia;
use App\Models\Equipo; // Necesario para loadAllData si lo mueves aquí
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
}