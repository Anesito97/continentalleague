<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameScore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function index()
    {
        // Hub Menu
        $keepyUppyTopScore = GameScore::where('game_type', 'keepy_uppy')->max('score') ?? 0;
        $penaltyTopScore = GameScore::where('game_type', 'penalty')->max('score') ?? 0;
        $porteroRunnerTopScore = GameScore::where('game_type', 'portero_runner')->max('score') ?? 0;

        return view('game.menu', compact('keepyUppyTopScore', 'penaltyTopScore', 'porteroRunnerTopScore'));
    }

    public function keepyUppy()
    {
        // Specific Game View
        $topScores = GameScore::where('game_type', 'keepy_uppy')
            ->with('user')
            ->orderBy('score', 'desc')
            ->take(10)
            ->get();

        $userBest = Auth::check()
            ? GameScore::where('user_id', Auth::id())->where('game_type', 'keepy_uppy')->max('score') ?? 0
            : 0;

        return view('game.keepy-uppy', compact('topScores', 'userBest'));
    }

    public function penalty()
    {
        // Penalty Game View
        $topScores = GameScore::where('game_type', 'penalty')
            ->with('user')
            ->orderBy('score', 'desc')
            ->take(10)
            ->get();

        $userBest = Auth::check()
            ? GameScore::where('user_id', Auth::id())->where('game_type', 'penalty')->max('score') ?? 0
            : 0;

        return view('game.penalty', compact('topScores', 'userBest'));
    }

    public function porteroRunner()
    {
        // Portero Runner Game View
        $topScores = GameScore::where('game_type', 'portero_runner')
            ->with('user')
            ->orderBy('score', 'desc')
            ->take(10)
            ->get();

        $userBest = Auth::check()
            ? GameScore::where('user_id', Auth::id())->where('game_type', 'portero_runner')->max('score') ?? 0
            : 0;

        return view('game.portero-runner', compact('topScores', 'userBest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'score' => 'required|integer|min:1',
            'game_type' => 'nullable|string|in:keepy_uppy,penalty,portero_runner'
        ]);

        $gameType = $request->game_type ?? 'keepy_uppy';
        $userId = Auth::id();

        return DB::transaction(function () use ($userId, $gameType, $request) {
            // Fetch all scores for this user and game type with a lock
            $existingScores = GameScore::where('user_id', $userId)
                ->where('game_type', $gameType)
                ->lockForUpdate()
                ->orderBy('score', 'desc')
                ->get();

            if ($existingScores->count() > 0) {
                // The first one is the best score because of orderBy desc
                $bestScoreRecord = $existingScores->first();

                // Delete any duplicates (all records except the first one)
                if ($existingScores->count() > 1) {
                    $duplicates = $existingScores->slice(1);
                    foreach ($duplicates as $duplicate) {
                        $duplicate->delete();
                    }
                }

                // Check if new score is higher than the best existing score
                if ((int) $request->score > (int) $bestScoreRecord->score) {
                    $bestScoreRecord->update(['score' => $request->score]);
                }
            } else {
                // No existing score, create new one
                GameScore::create([
                    'user_id' => $userId,
                    'score' => $request->score,
                    'game_type' => $gameType
                ]);
            }

            return response()->json(['success' => true]);
        });
    }
}
