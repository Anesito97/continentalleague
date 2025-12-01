<?php

namespace App\Http\Controllers;

use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LoadsCommonData;

class AnalyticsController extends Controller
{
    use LoadsCommonData;

    public function index()
    {
        session(['activeAdminContent' => 'analytics']);
        $data = $this->loadAllData();
        $data['news'] = $this->getEmptyNewsPaginator();
        $data['activeView'] = 'admin';

        // Agrupar vistas por URL y contar
        $data['pageViews'] = PageView::select('url', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_visit'))
            ->groupBy('url')
            ->orderByDesc('total')
            ->get();

        return view('index', $data);
    }
}
