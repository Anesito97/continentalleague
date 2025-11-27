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

    public function index()
    {
        $bestEleven = $this->idealElevenService->getBestEleven();

        return view('ideal-eleven.index', compact('bestEleven'));
    }
}
