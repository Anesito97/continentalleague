<?php

namespace Tests\Unit;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use App\Services\IdealElevenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdealElevenServiceTest extends TestCase
{
    use RefreshDatabase;

    private IdealElevenService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $ratingService = new \App\Services\PlayerRatingService();
        $this->service = new IdealElevenService($ratingService);
    }

    public function test_gk_scoring_logic()
    {
        // 1. Setup Team and GK
        $team = Equipo::create(['nombre' => 'GK Team', 'escudo_url' => 'url']);
        $visitorTeam = Equipo::create(['nombre' => 'Visitor', 'escudo_url' => 'url']);

        $gk = Jugador::create([
            'equipo_id' => $team->id,
            'nombre' => 'The Wall',
            'posicion_general' => 'portero',
            'numero' => 1,
            'paradas' => 10, // +20 pts (10 * 2)
            'goles' => 0,
            'asistencias' => 0,
            'amarillas' => 0,
            'rojas' => 0
        ]);

        // 2. Match 1: Lost 1-2 (Conceded 2, Lost)
        Partido::create([
            'jornada' => 1,
            'equipo_local_id' => $team->id,
            'equipo_visitante_id' => $visitorTeam->id,
            'goles_local' => 1,
            'goles_visitante' => 2,
            'estado' => 'finalizado',
            'fecha_hora' => now()
        ]);

        // 3. Match 2: Won 1-0 (Clean Sheet)
        Partido::create([
            'jornada' => 2,
            'equipo_local_id' => $team->id,
            'equipo_visitante_id' => $visitorTeam->id,
            'goles_local' => 1,
            'goles_visitante' => 0,
            'estado' => 'finalizado',
            'fecha_hora' => now()
        ]);

        // Expected Calculation:
        // Saves: 10 * 2 = 20
        // Goals Conceded: 2 (from Match 1) * 0.7 = 1.4 -> -1.4
        // Clean Sheets: 1 (Match 2) * 5 = 5 -> +5
        // Matches Lost: 1 (Match 1) * 0.3 = 0.3 -> -0.3

        // Total: 20 - 1.4 + 5 - 0.3 = 23.3

        $bestEleven = $this->service->getBestEleven($team->id);
        $bestGk = $bestEleven['goalkeeper'];

        $this->assertNotNull($bestGk);
        $this->assertEquals($gk->id, $bestGk->id);
        $this->assertEquals(23.3, $bestGk->rating);
    }
}
