<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\EventoPartido;
use App\Services\IdealElevenService;
use Illuminate\Http\Request;

class DeepAnalysisController extends Controller
{
    protected $idealElevenService;

    public function __construct(IdealElevenService $idealElevenService)
    {
        $this->idealElevenService = $idealElevenService;
    }

    public function index()
    {
        $teams = Equipo::orderBy('nombre')->get();
        return view('admin.analysis.index', compact('teams'));
    }

    public function selection(Request $request)
    {
        $request->validate([
            'my_team_id' => 'required|exists:equipos,id',
            'opponent_id' => 'required|exists:equipos,id',
        ]);

        $myTeam = Equipo::with('jugadores')->findOrFail($request->my_team_id);
        $opponent = Equipo::findOrFail($request->opponent_id);

        return view('admin.analysis.selection', compact('myTeam', 'opponent'));
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'my_team_id' => 'required|exists:equipos,id',
            'opponent_id' => 'required|exists:equipos,id|different:my_team_id',
        ]);

        $myTeam = Equipo::with('jugadores')->find($request->my_team_id);
        $opponent = Equipo::with('jugadores')->find($request->opponent_id);

        // 1. Historial H2H
        $h2hMatches = Partido::where(function ($q) use ($myTeam, $opponent) {
            $q->where('equipo_local_id', $myTeam->id)
                ->where('equipo_visitante_id', $opponent->id);
        })->orWhere(function ($q) use ($myTeam, $opponent) {
            $q->where('equipo_local_id', $opponent->id)
                ->where('equipo_visitante_id', $myTeam->id);
        })->where('estado', 'finalizado')->orderByDesc('fecha_hora')->take(10)->get();

        $h2hStats = [
            'wins' => 0,
            'draws' => 0,
            'losses' => 0,
            'my_goals' => 0,
            'opponent_goals' => 0
        ];

        foreach ($h2hMatches as $match) {
            $isLocal = $match->equipo_local_id == $myTeam->id;
            $myGoals = $isLocal ? $match->goles_local : $match->goles_visitante;
            $opGoals = $isLocal ? $match->goles_visitante : $match->goles_local;

            $h2hStats['my_goals'] += $myGoals;
            $h2hStats['opponent_goals'] += $opGoals;

            if ($myGoals > $opGoals)
                $h2hStats['wins']++;
            elseif ($myGoals == $opGoals)
                $h2hStats['draws']++;
            else
                $h2hStats['losses']++;
        }

        // 2. Advanced Metrics ("Tale of the Tape")
        $metrics = [
            'my' => $this->calculateTeamMetrics($myTeam),
            'op' => $this->calculateTeamMetrics($opponent)
        ];

        // 3. Goal Timing Analysis (Scored & Conceded)
        $goalTiming = [
            'my_scored' => $this->getGoalTiming($myTeam->id, 'scored'),
            'my_conceded' => $this->getGoalTiming($myTeam->id, 'conceded'),
            'op_scored' => $this->getGoalTiming($opponent->id, 'scored'),
            'op_conceded' => $this->getGoalTiming($opponent->id, 'conceded'),
        ];

        // 4. Win Probability Calculation
        $winProb = $this->calculateWinProbability($myTeam, $opponent, $h2hStats);

        // 5. Squad Stats & Key Players
        $squadStats = [
            'my' => $this->getSquadStats($myTeam),
            'op' => $this->getSquadStats($opponent)
        ];

        // 6. Recent History (Detailed)
        $recentHistory = [
            'my' => $this->getRecentMatches($myTeam),
            'op' => $this->getRecentMatches($opponent)
        ];

        // 7. Alineación Sugerida (Variantes)
        $availablePlayers = $request->input('available_players'); // Array of IDs or null
        $variants = $this->getLineupVariants($myTeam, $opponent, $metrics, $availablePlayers);

        // Use the first variant (Balanced/AI Best) for substitutions and marking
        $mainVariant = $variants[0]['lineup'];

        // 8. Substituciones Sugeridas (NUEVO)
        $substitutions = $this->getSubstitutions($myTeam, $mainVariant);

        // 9. Marcas Personales (NUEVO)
        $manMarking = $this->getManMarking($myTeam, $opponent);

        // 10. Generación de Estrategia "AI" (Expanded)
        $strategy = $this->generateStrategy($myTeam, $opponent, $h2hStats, $metrics, $goalTiming);

        // 11. Key Matchup (Duel of the Day)
        $myTopScorer = $myTeam->jugadores->sortByDesc('goles')->first();
        $opTopScorer = $opponent->jugadores->sortByDesc('goles')->first();

        $keyMatchup = null;
        if ($myTopScorer && $opTopScorer) {
            $keyMatchup = [
                'my' => $myTopScorer,
                'op' => $opTopScorer,
                'comparison' => [
                    'goals' => ['my' => $myTopScorer->goles, 'op' => $opTopScorer->goles],
                    'assists' => ['my' => $myTopScorer->asistencias, 'op' => $opTopScorer->asistencias],
                    'matches' => ['my' => $myTopScorer->partidos_jugados, 'op' => $opTopScorer->partidos_jugados],
                    'gpg' => [
                        'my' => $myTopScorer->partidos_jugados > 0 ? number_format($myTopScorer->goles / $myTopScorer->partidos_jugados, 2) : 0,
                        'op' => $opTopScorer->partidos_jugados > 0 ? number_format($opTopScorer->goles / $opTopScorer->partidos_jugados, 2) : 0
                    ]
                ]
            ];
        }

        return view('admin.analysis.show', compact(
            'myTeam',
            'opponent',
            'h2hMatches',
            'h2hStats',
            'metrics',
            'goalTiming',
            'winProb',
            'squadStats',
            'recentHistory',
            'variants',
            'substitutions',
            'manMarking',
            'strategy',
            'keyMatchup'
        ));
    }

    private function getLineupVariants($myTeam, $opponent, $metrics, $availablePlayers)
    {
        $variants = [];

        // Variant 1: AI Best (Dynamic based on opponent)
        $aiFormation = $this->determineFormation($myTeam, $opponent, $metrics);
        $variants[] = [
            'type' => 'IA Recomendada (Equilibrada)',
            'formation' => $aiFormation['formation'],
            'reasoning' => $aiFormation['reasoning'],
            'lineup' => $this->flattenLineup($this->idealElevenService->getBestEleven($myTeam->id, $aiFormation['config'], true, $availablePlayers))
        ];

        // Variant 2: Offensive (Aggressive)
        $variants[] = [
            'type' => 'Ofensiva Total',
            'formation' => '3-4-3',
            'reasoning' => 'Arriesga atrás para saturar el ataque. Ideal si necesitas remontar o el rival se encierra.',
            'lineup' => $this->flattenLineup($this->idealElevenService->getBestEleven($myTeam->id, ['def' => 3, 'mid' => 4, 'fwd' => 3], true, $availablePlayers))
        ];

        // Variant 3: Defensive (Bus Parking)
        $variants[] = [
            'type' => 'Muralla Defensiva',
            'formation' => '5-4-1',
            'reasoning' => 'Prioriza mantener el cero. Bloque bajo y contragolpes con un solo punta.',
            'lineup' => $this->flattenLineup($this->idealElevenService->getBestEleven($myTeam->id, ['def' => 5, 'mid' => 4, 'fwd' => 1], true, $availablePlayers))
        ];

        return $variants;
    }

    private function flattenLineup($lineupRaw)
    {
        return collect([$lineupRaw['goalkeeper']])
            ->merge($lineupRaw['defenders'])
            ->merge($lineupRaw['midfielders'])
            ->merge($lineupRaw['forwards'])
            ->filter();
    }

    private function determineFormation($myTeam, $opponent, $metrics)
    {
        // Simple heuristic for formation selection
        $opStrength = $metrics['op']['ppg'];
        $myStrength = $metrics['my']['ppg'];
        $opGoals = $metrics['op']['gf_pg'];

        // Default: Balanced
        $formation = '4-4-2';
        $config = ['def' => 4, 'mid' => 4, 'fwd' => 2];
        $reasoning = "El rival presenta un nivel similar. Un 4-4-2 clásico ofrece equilibrio para controlar el medio campo sin renunciar al ataque.";

        // Scenario 1: Opponent is much stronger (Defensive)
        if ($opStrength > $myStrength + 0.5 || $opGoals > 2.0) {
            $formation = '5-4-1';
            $config = ['def' => 5, 'mid' => 4, 'fwd' => 1];
            $reasoning = "El rival es muy ofensivo (promedia {$opGoals} goles). Recomendamos un 5-4-1 para saturar la defensa y buscar contragolpes rápidos.";
        }
        // Scenario 2: Opponent is weaker (Offensive)
        elseif ($opStrength < $myStrength - 0.5) {
            $formation = '3-4-3';
            $config = ['def' => 3, 'mid' => 4, 'fwd' => 3];
            $reasoning = "El rival es accesible. Un 3-4-3 agresivo permitirá dominar la posesión y crear múltiples ocasiones de gol.";
        }
        // Scenario 3: Balanced but we want more control (4-3-3)
        elseif ($metrics['my']['gf_pg'] > 1.5) {
            $formation = '4-3-3';
            $config = ['def' => 4, 'mid' => 3, 'fwd' => 3];
            $reasoning = "Tu equipo tiene buen poder ofensivo. El 4-3-3 potenciará a tus extremos y mantendrá presión alta.";
        }

        return ['formation' => $formation, 'config' => $config, 'reasoning' => $reasoning];
    }

    private function getSubstitutions($team, $starters)
    {
        // Find players NOT in the starters list
        $starterIds = $starters->pluck('id')->toArray();
        $bench = $team->jugadores->whereNotIn('id', $starterIds);

        $subs = [];

        // 1. Offensive Sub (If losing)
        $bestAttacker = $bench->whereIn('posicion_general', ['DEL', 'MED'])->sortByDesc('goles')->first();
        if ($bestAttacker) {
            $subs[] = [
                'scenario' => 'Si vas perdiendo (Min 60-70)',
                'in' => $bestAttacker,
                'out_position' => 'DEF/MED',
                'reason' => "Aporta frescura y gol ({$bestAttacker->goles} goles)."
            ];
        }

        // 2. Defensive Sub (If winning)
        $bestDefender = $bench->whereIn('posicion_general', ['DEF', 'MED'])->sortByDesc('partidos_jugados')->first();
        if ($bestDefender) {
            $subs[] = [
                'scenario' => 'Si vas ganando (Min 75+)',
                'in' => $bestDefender,
                'out_position' => 'DEL',
                'reason' => 'Cierra el partido y asegura el resultado.'
            ];
        }

        return $subs;
    }

    private function getManMarking($myTeam, $opponent)
    {
        $opThreat = $opponent->jugadores->sortByDesc('goles')->first();
        $myStopper = $myTeam->jugadores->where('posicion_general', 'DEF')->sortByDesc('partidos_jugados')->first();

        if ($opThreat && $myStopper) {
            return [
                'target' => $opThreat,
                'marker' => $myStopper,
                'instruction' => "Asigna a {$myStopper->nombre} para marcar de cerca a {$opThreat->nombre}. Evita que reciba cómodo."
            ];
        }
        return null;
    }

    private function calculateTeamMetrics($team)
    {
        $matches = $team->partidos_jugados > 0 ? $team->partidos_jugados : 1;

        // Basic Stats
        $cleanSheets = Partido::where(function ($q) use ($team) {
            $q->where('equipo_local_id', $team->id)->where('goles_visitante', 0);
        })->orWhere(function ($q) use ($team) {
            $q->where('equipo_visitante_id', $team->id)->where('goles_local', 0);
        })->where('estado', 'finalizado')->count();

        $failedToScore = Partido::where(function ($q) use ($team) {
            $q->where('equipo_local_id', $team->id)->where('goles_local', 0);
        })->orWhere(function ($q) use ($team) {
            $q->where('equipo_visitante_id', $team->id)->where('goles_visitante', 0);
        })->where('estado', 'finalizado')->count();

        // BTTS & Over 2.5
        $btts = Partido::where(function ($q) use ($team) {
            $q->where('equipo_local_id', $team->id)->where('goles_local', '>', 0)->where('goles_visitante', '>', 0);
        })->orWhere(function ($q) use ($team) {
            $q->where('equipo_visitante_id', $team->id)->where('goles_visitante', '>', 0)->where('goles_local', '>', 0);
        })->where('estado', 'finalizado')->count();

        $over25 = Partido::where(function ($q) use ($team) {
            $q->where('equipo_local_id', $team->id)->orWhere('equipo_visitante_id', $team->id);
        })->whereRaw('(goles_local + goles_visitante) > 2.5')
            ->where('estado', 'finalizado')->count();

        // Home/Away Splits
        $homeMatches = Partido::where('equipo_local_id', $team->id)->where('estado', 'finalizado')->get();
        $awayMatches = Partido::where('equipo_visitante_id', $team->id)->where('estado', 'finalizado')->get();

        $homeGF = $homeMatches->sum('goles_local');
        $homeGA = $homeMatches->sum('goles_visitante');
        $awayGF = $awayMatches->sum('goles_visitante');
        $awayGA = $awayMatches->sum('goles_local');

        // Discipline
        $yellows = EventoPartido::where('equipo_id', $team->id)->where('tipo_evento', 'tarjeta_amarilla')->count();
        $reds = EventoPartido::where('equipo_id', $team->id)->where('tipo_evento', 'tarjeta_roja')->count();

        // Records
        $biggestWin = 'N/A';
        $biggestLoss = 'N/A';
        // (Logic for records could be added here if needed, keeping it simple for now to avoid query overload)

        // Home/Away PPG & Personality
        $homeMatchesCount = $homeMatches->count();
        $awayMatchesCount = $awayMatches->count();

        $homePoints = 0;
        foreach ($homeMatches as $m) {
            if ($m->goles_local > $m->goles_visitante)
                $homePoints += 3;
            elseif ($m->goles_local == $m->goles_visitante)
                $homePoints += 1;
        }

        $awayPoints = 0;
        foreach ($awayMatches as $m) {
            if ($m->goles_visitante > $m->goles_local)
                $awayPoints += 3;
            elseif ($m->goles_visitante == $m->goles_local)
                $awayPoints += 1;
        }

        $homePPG = $homeMatchesCount > 0 ? $homePoints / $homeMatchesCount : 0;
        $awayPPG = $awayMatchesCount > 0 ? $awayPoints / $awayMatchesCount : 0;

        $personality = "Equilibrado";
        $personalityColor = "text-gray-400";
        $personalityIcon = "fa-scale-balanced";

        if ($homePPG > 2.0 && $awayPPG < 1.0) {
            $personality = "Fortín Local";
            $personalityColor = "text-green-400";
            $personalityIcon = "fa-dungeon";
        } elseif ($awayPPG > 2.0 && $homePPG < 1.0) {
            $personality = "Visitante Peligroso";
            $personalityColor = "text-red-400";
            $personalityIcon = "fa-road";
        } elseif ($homePPG > 2.0 && $awayPPG > 2.0) {
            $personality = "Aplanadora Total";
            $personalityColor = "text-yellow-400";
            $personalityIcon = "fa-crown";
        } elseif ($homePPG < 0.5 && $awayPPG < 0.5) {
            $personality = "En Crisis";
            $personalityColor = "text-red-600";
            $personalityIcon = "fa-triangle-exclamation";
        }

        return [
            'ppg' => number_format($team->puntos / $matches, 2),
            'gf_pg' => number_format($team->goles_a_favor / $matches, 2),
            'ga_pg' => number_format($team->goles_en_contra / $matches, 2),
            'clean_sheets' => $cleanSheets,
            'clean_sheets_pct' => number_format(($cleanSheets / $matches) * 100, 0) . '%',
            'failed_to_score' => $failedToScore,
            'failed_to_score_pct' => number_format(($failedToScore / $matches) * 100, 0) . '%',
            'btts_pct' => number_format(($btts / $matches) * 100, 0) . '%',
            'over25_pct' => number_format(($over25 / $matches) * 100, 0) . '%',
            'home_gf' => number_format($homeMatchesCount > 0 ? $homeGF / $homeMatchesCount : 0, 2),
            'home_ga' => number_format($homeMatchesCount > 0 ? $homeGA / $homeMatchesCount : 0, 2),
            'away_gf' => number_format($awayMatchesCount > 0 ? $awayGF / $awayMatchesCount : 0, 2),
            'away_ga' => number_format($awayMatchesCount > 0 ? $awayGA / $awayMatchesCount : 0, 2),
            'home_ppg' => number_format($homePPG, 2),
            'away_ppg' => number_format($awayPPG, 2),
            'personality' => [
                'label' => $personality,
                'color' => $personalityColor,
                'icon' => $personalityIcon
            ],
            'yellows' => $yellows,
            'reds' => $reds,
            'form_score' => $this->calculateFormScore($team->form_guide)
        ];
    }

    private function calculateFormScore($form)
    {
        if (!$form)
            return 0;
        $points = 0;
        $chars = str_split($form);
        foreach ($chars as $char) {
            if ($char === 'G')
                $points += 3;
            if ($char === 'E')
                $points += 1;
        }
        return $points; // Max 15 for last 5 games
    }

    private function getGoalTiming($teamId, $type = 'scored')
    {
        $segments = [
            '0-15' => 0,
            '16-30' => 0,
            '31-45' => 0,
            '46-60' => 0,
            '61-75' => 0,
            '76-90+' => 0
        ];

        if ($type === 'scored') {
            $goals = EventoPartido::where('equipo_id', $teamId)
                ->where('tipo_evento', 'gol')
                ->get();
        } else {
            // Conceded: Find matches where team played, then find goals NOT by team
            $goals = EventoPartido::where('tipo_evento', 'gol')
                ->where('equipo_id', '!=', $teamId)
                ->whereHas('partido', function ($q) use ($teamId) {
                    $q->where('equipo_local_id', $teamId)
                        ->orWhere('equipo_visitante_id', $teamId);
                })->get();
        }

        foreach ($goals as $goal) {
            $min = $goal->minuto;
            if ($min <= 15)
                $segments['0-15']++;
            elseif ($min <= 30)
                $segments['16-30']++;
            elseif ($min <= 45)
                $segments['31-45']++;
            elseif ($min <= 60)
                $segments['46-60']++;
            elseif ($min <= 75)
                $segments['61-75']++;
            else
                $segments['76-90+']++;
        }

        $total = $goals->count();
        $percentages = [];
        foreach ($segments as $key => $count) {
            $percentages[$key] = $total > 0 ? round(($count / $total) * 100) : 0;
        }

        return ['counts' => $segments, 'percentages' => $percentages, 'total' => $total];
    }

    private function getSquadStats($team)
    {
        $totalGoals = $team->jugadores->sum('goles');
        $totalAssists = $team->jugadores->sum('asistencias');
        $topScorer = $team->jugadores->sortByDesc('goles')->first();
        $topAssister = $team->jugadores->sortByDesc('asistencias')->first();

        // --- DNA INSIGHT (GOALS) ---
        $dnaReasoning = "Plantilla equilibrada.";
        $dnaStrategy = "Mantener orden defensivo estándar.";

        if ($totalGoals > 0 && $topScorer) {
            $scorerPct = ($topScorer->goles / $totalGoals) * 100;
            if ($scorerPct > 40) {
                $dnaReasoning = "Dependencia extrema de {$topScorer->nombre} ({$topScorer->goles} goles).";
                $dnaStrategy = "Doble marca sobre él. Si lo anulas, el equipo se apaga.";
            } elseif ($scorerPct > 20) {
                $dnaReasoning = "Ataque concentrado en sus puntas.";
                $dnaStrategy = "Vigilar desmarques de ruptura de los delanteros.";
            } else {
                $dnaReasoning = "Amenaza distribuida. Cualquiera puede marcar.";
                $dnaStrategy = "Defensa zonal estricta. No perder referencias individuales.";
            }
        }

        // --- CREATIVE INSIGHT (ASSISTS) ---
        $creativeReasoning = "Juego directo o individual.";
        $creativeStrategy = "Ganar duelos 1vs1.";

        if ($totalAssists > 0 && $topAssister) {
            $assisterPct = ($topAssister->asistencias / $totalAssists) * 100;
            if ($assisterPct > 40) {
                $creativeReasoning = "Motor único: {$topAssister->nombre} genera todo el juego.";
                $creativeStrategy = "Presión asfixiante sobre el '10'. No dejarle girar.";
            } else {
                $creativeReasoning = "Juego asociativo y coral.";
                $creativeStrategy = "Cortar líneas de pase y saturar el medio campo.";
            }
        }

        return [
            'size' => $team->jugadores->count(),
            'top_scorers' => $team->jugadores->sortByDesc('goles')->take(3),
            'top_assisters' => $team->jugadores->sortByDesc('asistencias')->take(3),
            'goalkeeper' => $team->jugadores->where('posicion_general', 'POR')->sortByDesc('paradas')->first(),
            'total_goals' => $totalGoals,
            'total_assists' => $totalAssists,
            'dna_insight' => ['reason' => $dnaReasoning, 'strategy' => $dnaStrategy],
            'creative_insight' => ['reason' => $creativeReasoning, 'strategy' => $creativeStrategy],
        ];
    }

    private function getRecentMatches($team)
    {
        return Partido::with(['localTeam', 'visitorTeam'])
            ->where(function ($q) use ($team) {
                $q->where('equipo_local_id', $team->id)->orWhere('equipo_visitante_id', $team->id);
            })
            ->where('estado', 'finalizado')
            ->orderByDesc('fecha_hora')
            ->take(5)
            ->get()
            ->map(function ($match) use ($team) {
                $isLocal = $match->equipo_local_id == $team->id;
                $opponent = $isLocal ? $match->visitorTeam : $match->localTeam;
                $myGoals = $isLocal ? $match->goles_local : $match->goles_visitante;
                $opGoals = $isLocal ? $match->goles_visitante : $match->goles_local;

                $result = 'E';
                if ($myGoals > $opGoals)
                    $result = 'G';
                elseif ($myGoals < $opGoals)
                    $result = 'P';

                return [
                    'opponent' => $opponent ? $opponent->nombre : 'Desconocido',
                    'result' => $result,
                    'score' => "$myGoals - $opGoals",
                    'date' => $match->fecha_hora ? $match->fecha_hora->format('d/m') : '-'
                ];
            });
    }

    private function calculateWinProbability($myTeam, $opponent, $h2hStats)
    {
        $myScore = 50; // Base

        // 1. Form Impact (+/- 15%)
        $myForm = $this->calculateFormScore($myTeam->form_guide);
        $opForm = $this->calculateFormScore($opponent->form_guide);
        $myScore += ($myForm - $opForm) * 1.5;

        // 2. League Position Impact (+/- 10%)
        // Assuming higher points = better.
        $pointDiff = $myTeam->puntos - $opponent->puntos;
        $myScore += ($pointDiff / 2);

        // 3. H2H Impact (+/- 10%)
        if ($h2hStats['wins'] + $h2hStats['losses'] > 0) {
            $h2hDiff = $h2hStats['wins'] - $h2hStats['losses'];
            $myScore += $h2hDiff * 3;
        }

        // Clamp between 5 and 95
        return min(max(round($myScore), 5), 95);
    }

    private function generateStrategy($myTeam, $opponent, $h2hStats, $metrics, $goalTiming)
    {
        $attack = [];
        $defense = [];
        $alerts = [];

        // --- 1. ATTACK PHASE (How to score) ---
        $opGA = $metrics['op']['ga_pg'];
        $opCleanSheets = $metrics['op']['clean_sheets'];

        if ($opGA > 1.8) {
            $attack[] = "La defensa rival es frágil (recibe {$opGA} goles/partido). Presiona alto y busca el error.";
        } elseif ($opGA < 0.8) {
            $attack[] = "Rival muy sólido. Paciencia, evita centros frontales y busca filtrar pases entre líneas.";
        }

        if ($opCleanSheets == 0) {
            $attack[] = "No saben mantener el cero. Dispara desde fuera del área ante cualquier espacio.";
        }

        // Check for late goals conceded
        if (isset($goalTiming['op_conceded']['percentages']['76-90+']) && $goalTiming['op_conceded']['percentages']['76-90+'] > 25) {
            $attack[] = "Sufren físicamente al final. Guarda cambios ofensivos para los últimos 15 minutos.";
        }

        // --- 2. DEFENSE PHASE (How to stop them) ---
        $opGF = $metrics['op']['gf_pg'];
        $opFailedScore = $metrics['op']['failed_to_score'];

        if ($opGF > 2.0) {
            $defense[] = "Poder ofensivo letal. Prioriza el orden defensivo y no arriesgues en la salida.";
        } elseif ($opGF < 0.8) {
            $defense[] = "Les cuesta marcar. Si anotas primero, el partido es tuyo.";
        }

        if ($opFailedScore > 2) {
            $defense[] = "Son inconsistentes arriba. Si los frustras los primeros 20 min, se desesperan.";
        }

        // Check for early goals scored
        if (isset($goalTiming['op_scored']['percentages']['0-15']) && $goalTiming['op_scored']['percentages']['0-15'] > 20) {
            $defense[] = "Entran muy enchufados. Máxima concentración en el arranque.";
        }

        // --- 3. ALERTS (Discipline, H2H, etc) ---
        $opYellows = $metrics['op']['yellows'];
        $opReds = $metrics['op']['reds'];

        if ($opYellows > 10 || $opReds > 1) {
            $alerts[] = "Juegan al límite (Alto riesgo de tarjetas). Provoca faltas cerca del área.";
        }

        if ($h2hStats['losses'] >= 2 && $h2hStats['wins'] == 0) {
            $alerts[] = "Rival histórico difícil ('Bestia Negra'). Juega con cabeza fría.";
        }

        // Fillers if empty
        if (empty($attack))
            $attack[] = "Juega tu fútbol habitual. Busca imponer tu ritmo.";
        if (empty($defense))
            $defense[] = "Mantén las líneas juntas y comunícate constantemente.";
        if (empty($alerts))
            $alerts[] = "Partido estándar. Sin anomalías estadísticas graves.";

        return [
            'attack' => $attack,
            'defense' => $defense,
            'alerts' => $alerts
        ];
    }
}
