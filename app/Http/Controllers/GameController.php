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
        // Get Top 10 High Scores
        $topScores = GameScore::with('user')
            ->select('user_id', DB::raw('MAX(score) as max_score'))
            ->groupBy('user_id')
            ->orderByDesc('max_score')
            ->take(10)
            ->get();

        // Get User's Best Score
        $userBest = 0;
        if (Auth::check()) {
            $userBest = GameScore::where('user_id', Auth::id())->max('score') ?? 0;
        }

        return view('game.index', compact('topScores', 'userBest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'score' => 'required|integer|min:0',
        ]);

        $score = $request->input('score');
        $user = Auth::user();

        // Save score
        GameScore::create([
            'user_id' => $user->id,
            'score' => $score,
        ]);

        return response()->json(['success' => true, 'message' => 'Score saved!']);
    }
}
