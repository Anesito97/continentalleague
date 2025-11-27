<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Jugador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LineupBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_lineup_builder_index_loads()
    {
        $response = $this->get(route('lineup.index'));
        $response->assertStatus(200);
        $response->assertViewIs('lineup.index');
    }

    public function test_lineup_builder_api_returns_players()
    {
        $team = Equipo::create(['nombre' => 'Test Team', 'escudo_url' => 'url']);
        $player = Jugador::create([
            'equipo_id' => $team->id,
            'nombre' => 'Test Player',
            'posicion_general' => 'Delantero',
            'numero' => 9
        ]);

        $response = $this->get(route('lineup.players', $team->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['nombre' => 'Test Player']);
    }
}
