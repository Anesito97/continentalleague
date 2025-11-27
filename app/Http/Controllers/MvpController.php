<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Services\MvpService;
use Illuminate\Http\Request;

class MvpController extends Controller
{
    protected $mvpService;

    public function __construct(MvpService $mvpService)
    {
        $this->mvpService = $mvpService;
    }

    public function index()
    {
        // Get all distinct jornadas
        $jornadas = Partido::select('jornada')
            ->distinct()
            ->orderBy('jornada', 'asc')
            ->pluck('jornada');

        $mvpData = [];

        foreach ($jornadas as $jornada) {
            // Check if all matches in this jornada are finished
            $matches = Partido::where('jornada', $jornada)->get();
            $totalMatches = $matches->count();
            $finishedMatches = $matches->where('estado', 'finalizado')->count();

            $isFinished = ($totalMatches > 0 && $totalMatches === $finishedMatches);

            $mvp = null;
            if ($isFinished) {
                $mvp = $this->mvpService->getMvpForJornada($jornada);
            }

            $mvpData[] = [
                'jornada' => $jornada,
                'is_finished' => $isFinished,
                'mvp' => $mvp,
                'status_label' => $isFinished ? 'Finalizada' : ($finishedMatches > 0 ? 'En Progreso' : 'Pendiente')
            ];
        }

        return view('mvp.index', compact('mvpData'));
    }
}
