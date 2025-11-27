<?php

namespace Tests\Unit;

use App\Models\Equipo;
use App\Models\EventoPartido;
use App\Models\Jugador;
use App\Models\Partido;
use App\Services\MvpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MvpServiceTest extends TestCase
{
    use RefreshDatabase;

    private MvpService $mvpService;

    protected function setUp(): void
    {
        parent::setUp();
        // Use real service for integration test
        $ratingService = new \App\Services\PlayerRatingService();
        $this->mvpService = new MvpService($ratingService);
    }

    public function test_mvp_calculation_for_forward_hat_trick()
    {
        $this->seedData();

        // Create Team
        $team = Equipo::create(['nombre' => 'Team A', 'escudo_url' => 'url']);
        $visitorTeam = Equipo::create(['nombre' => 'Visitor A', 'escudo_url' => 'url']);

        // Create a forward with a hat-trick
        $forward = Jugador::create([
            'equipo_id' => $team->id,
            'nombre' => 'Striker',
            'posicion_general' => 'Delantero',
            'numero' => 9
        ]);

        $match = Partido::create([
            'jornada' => 1,
            'equipo_local_id' => $team->id,
            'equipo_visitante_id' => $visitorTeam->id,
            'goles_local' => 3,
            'goles_visitante' => 0,
            'fecha_hora' => now(),
            'estado' => 'finalizado'
        ]);

        // 3 Goals
        for ($i = 0; $i < 3; $i++) {
            EventoPartido::create([
                'partido_id' => $match->id,
                'jugador_id' => $forward->id,
                'equipo_id' => $forward->equipo_id,
                'tipo_evento' => 'gol',
                'minuto' => 10 + $i
            ]);
        }

        $mvp = $this->mvpService->getMvpForJornada(1);

        $this->assertEquals($forward->id, $mvp->id);
        // Score: 3 goals * 10 = 30
        $this->assertEquals(30, $mvp->rating);
        $this->assertEquals('Hat-trick', $mvp->mvp_reason);
    }

    public function test_mvp_calculation_for_goalkeeper_clean_sheet_and_saves()
    {
        $this->seedData();

        // Create Team
        $team = Equipo::create(['nombre' => 'Team GK', 'escudo_url' => 'url']);
        $visitorTeam = Equipo::create(['nombre' => 'Visitor GK', 'escudo_url' => 'url']);

        // Create a GK
        $gk = Jugador::create([
            'equipo_id' => $team->id,
            'nombre' => 'Keeper',
            'posicion_general' => 'Portero',
            'numero' => 1
        ]);

        // Clean sheet match (0-0)
        $match = Partido::create([
            'jornada' => 2,
            'equipo_local_id' => $team->id,
            'equipo_visitante_id' => $visitorTeam->id,
            'goles_local' => 0,
            'goles_visitante' => 0,
            'fecha_hora' => now(),
            'estado' => 'finalizado'
        ]);

        // 5 Saves
        for ($i = 0; $i < 5; $i++) {
            EventoPartido::create([
                'partido_id' => $match->id,
                'jugador_id' => $gk->id,
                'equipo_id' => $gk->equipo_id,
                'tipo_evento' => 'parada',
                'minuto' => 10 + $i
            ]);
        }

        $mvp = $this->mvpService->getMvpForJornada(2);

        $this->assertEquals($gk->id, $mvp->id);
        // Score: (5 saves * 2) + 5 (Clean Sheet) = 10 + 5 = 15
        $this->assertEquals(15, $mvp->rating);
        $this->assertEquals('Muro Impenetrable', $mvp->mvp_reason);
    }

    public function test_mvp_calculation_own_goal_penalty()
    {
        $this->seedData();

        $team = Equipo::create(['nombre' => 'Team Bad', 'escudo_url' => 'url']);
        $visitorTeam = Equipo::create(['nombre' => 'Visitor Bad', 'escudo_url' => 'url']);

        $defender = Jugador::create([
            'equipo_id' => $team->id,
            'nombre' => 'BadLuck',
            'posicion_general' => 'Defensa',
            'numero' => 4
        ]);

        $match = Partido::create([
            'jornada' => 3,
            'equipo_local_id' => $team->id,
            'equipo_visitante_id' => $visitorTeam->id,
            'goles_local' => 0,
            'goles_visitante' => 1,
            'fecha_hora' => now(),
            'estado' => 'finalizado'
        ]);

        // 1 Own Goal
        EventoPartido::create([
            'partido_id' => $match->id,
            'jugador_id' => $defender->id,
            'equipo_id' => $defender->equipo_id,
            'tipo_evento' => 'gol',
            'goal_type' => 'en contra',
            'minuto' => 50
        ]);

        $mvp = $this->mvpService->getMvpForJornada(3);

        // Score: -10 (Own Goal) - 1.5 (Conceded) = -11.5
        $this->assertEquals(-11.5, $mvp->rating);
    }

    private function seedData()
    {
        // Basic setup if needed, factories handle most
    }
}
