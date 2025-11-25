<?php

namespace App\Traits;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Auth;
use App\Models\Vote;

trait LoadsCommonData
{
    // Función central que carga TODOS los datos necesarios
    public function loadAllData()
    {
        // FIX CRÍTICO: Cargar TODOS los jugadores para rankings globales.
        $players = Jugador::with('equipo')->get();

        // Carga de equipos y cálculo de la guía de forma (se mantiene)
        $teams = Equipo::orderByDesc('puntos')
            ->orderByRaw('(goles_a_favor - goles_en_contra) DESC')
            ->orderByDesc('goles_a_favor')
            ->get();

        // Obtener la jornada activa (la más baja con partidos pendientes)
        $activeJornada = Partido::where('estado', 'pendiente')->min('jornada');

        $pendingMatches = Partido::with(['localTeam', 'visitorTeam'])
            ->where('estado', 'pendiente')
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $finalicedMatches = Partido::with(['localTeam', 'visitorTeam'])
            ->where('estado', 'finalizado')
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $recentMatches = Partido::with([
            'localTeam',
            'visitorTeam',
            'eventos.jugador',
            'eventos.equipo',
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

        $playersWithCorrectGoals = $players->map(function ($player) {
            // Contamos solo los eventos de tipo 'gol' que NO son 'en contra'
            $validGoals = $player->eventos
                ->where('tipo_evento', 'gol')
                ->where(function ($event) {
                    // strtolower para asegurar que funcione con 'en contra' o 'En Contra'
                    return strtolower($event->goal_type ?? '') !== 'en contra';
                })
                ->count();

            // Sobrescribimos temporalmente la propiedad 'goles' del jugador con el valor correcto
            $player->goles = $validGoals;

            return $player;
        });
        // Goleadores: Orden descendente por Goles (solo si tienen > 0)
        $topScorers = $playersWithCorrectGoals->filter(fn($p) => $p->goles > 0)->sortByDesc('goles');

        // Asistentes: Orden descendente por Asistencias (solo si tienen > 0)
        $topAssists = $players->filter(fn($p) => $p->asistencias > 0)->sortByDesc('asistencias');

        // Porteros: Orden descendente por Paradas (solo si son porteros y tienen > 0)
        $topKeepers = $players->filter(fn($p) => $p->paradas > 0)->sortByDesc('paradas');

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
        $sliderMatches = collect();

        if ($nextMatch) {
            $upcomingMatches = $pendingMatches->take(3);

            // 2. Preparamos una colección con el partido Y sus datos calculados individualmente
            $sliderMatches = $upcomingMatches->map(function ($match) {
                // Calculamos la predicción para ESTE partido específico
                $prediction = $this->getMatchPrediction($match->localTeam, $match->visitorTeam);
                $h2hRecord = $this->calculateH2H($match->localTeam, $match->visitorTeam);

                // Datos de votación
                $votes = VoteController::getVotes($match->id);
                $totalVotes = array_sum($votes);
                $hasVoted = false;
                $userVote = null;

                if (Auth::check()) {
                    $voteRecord = Vote::where('user_id', Auth::id())
                        ->where('match_id', $match->id)
                        ->first();
                    if ($voteRecord) {
                        $hasVoted = true;
                        $userVote = $voteRecord->vote;
                    }
                }

                return (object) [
                    'match' => $match,
                    'prediction' => $prediction,
                    'h2hRecord' => $h2hRecord,
                    'probTitle' => $prediction['title'],
                    'votes' => $votes,
                    'totalVotes' => $totalVotes,
                    'hasVoted' => $hasVoted,
                    'userVote' => $userVote,
                ];
            });
        }

        $communityVotes = VoteController::getVotes($nextMatch->id ?? null);
        $communityTotal = array_sum($communityVotes);

        $communityLocalProb = $communityTotal > 0 ? number_format(($communityVotes['local'] / $communityTotal) * 100, 0) : 33;
        $communityVisitorProb = $communityTotal > 0 ? number_format(($communityVotes['visitor'] / $communityTotal) * 100, 0) : 33;
        $communityDrawProb = 100 - $communityLocalProb - $communityVisitorProb; // El restante

        $positions = $this->getPositions();
        $injuredPlayers = $players->where('esta_lesionado', true);

        return compact(
            'teams',
            'players',
            'pendingMatches',
            'finalicedMatches',
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
            'communityTotal',
            'positions',
            'injuredPlayers',
            'sliderMatches',
        );
    }

    public function calculateH2H(Equipo $team1, Equipo $team2)
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

    public function getRecentForm(Equipo $team, int $limit = 3)
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

    public function getMatchPrediction(Equipo $localTeam, Equipo $visitorTeam)
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
                'title' => 'Probabilidad (Estimada)',
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
        if ($totalPower == 0) {
            $totalPower = 1;
        }

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
            'title' => 'Probabilidad (Estimada)',
        ];
    }

    public function getPositions()
    {
        return [
            'Portero' => [
                'POR' => 'Portero',
            ],
            'Defensa' => [
                'DFC' => 'Defensa Central',
                'LI' => 'Lateral Izquierdo',
                'LD' => 'Lateral Derecho',
                'CAR' => 'Carrilero',
            ],
            'Medio' => [
                'MCD' => 'Mediocentro Defensivo',
                'MC' => 'Mediocentro',
                'MCO' => 'Mediocentro Ofensivo',
                'MI' => 'Interior Izquierdo',
                'MD' => 'Interior Derecho',
            ],
            'Delantero' => [
                'EI' => 'Extremo Izquierdo',
                'ED' => 'Extremo Derecho',
                'SD' => 'Segundo Delantero',
                'DC' => 'Delantero Centro',
            ],
        ];
    }

    public function getEmptyNewsPaginator()
    {
        return new \Illuminate\Pagination\LengthAwarePaginator(
            items: new \Illuminate\Support\Collection,
            total: 0,
            perPage: 10,
            currentPage: 1
        );
    }
}
