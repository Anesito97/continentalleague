<?php

namespace App\Http\Controllers;

use App\Services\IdealElevenService;
use Illuminate\Http\Request;

class IdealElevenController extends Controller
{
    protected $idealElevenService;

    public function __construct(IdealElevenService $idealElevenService)
    {
        $this->idealElevenService = $idealElevenService;
    }

    public function index(Request $request)
    {
        $teamId = $request->query('team_id');
        $team = null;

        if ($teamId) {
            $team = \App\Models\Equipo::find($teamId);
        }

        $bestEleven = $this->idealElevenService->getBestEleven($teamId);

        return view('ideal-eleven.index', compact('bestEleven', 'team'));
    }
}
