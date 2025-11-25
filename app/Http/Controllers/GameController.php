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

        return view('game.menu', compact('keepyUppyTopScore', 'penaltyTopScore'));
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

    public function store(Request $request)
    {
        $request->validate([
            'score' => 'required|integer|min:1',
            'game_type' => 'nullable|string|in:keepy_uppy,penalty'
        ]);

        GameScore::create([
            'user_id' => Auth::id(),
            'score' => $request->score,
            'game_type' => $request->game_type ?? 'keepy_uppy'
        ]);

        return response()->json(['success' => true]);
    }
}
