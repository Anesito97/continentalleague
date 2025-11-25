<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GameScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameScoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_score_is_updated_only_if_higher()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Post initial score
        $response = $this->postJson('/game/save', [
            'score' => 10,
            'game_type' => 'keepy_uppy'
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('game_scores', [
            'user_id' => $user->id,
            'score' => 10,
            'game_type' => 'keepy_uppy'
        ]);

        // 2. Post higher score (should update)
        $response = $this->postJson('/game/save', [
            'score' => 20,
            'game_type' => 'keepy_uppy'
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('game_scores', [
            'user_id' => $user->id,
            'score' => 20,
            'game_type' => 'keepy_uppy'
        ]);

        // Ensure no duplicate records
        $this->assertEquals(1, GameScore::where('user_id', $user->id)->count());

        // 3. Post lower score (should NOT update)
        $response = $this->postJson('/game/save', [
            'score' => 15,
            'game_type' => 'keepy_uppy'
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('game_scores', [
            'user_id' => $user->id,
            'score' => 20,
            'game_type' => 'keepy_uppy'
        ]);
    }
}
