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

        // 1. Métricas Generales
        $data['totalVisits'] = PageView::count();
        $data['uniqueVisitors'] = PageView::distinct('ip_address')->count('ip_address');

        // 2. Visitas por URL (Top Pages)
        $data['pageViews'] = PageView::select('url', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_visit'))
            ->groupBy('url')
            ->orderByDesc('total')
            ->limit(20) // Limitamos a las top 20 para no saturar
            ->get();

        // 3. Visitas por Día (Últimos 7 días)
        $visitsPerDay = PageView::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Rellenar días vacíos si es necesario (opcional, pero recomendado para gráficos)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayData = $visitsPerDay->firstWhere('date', $date);
            $chartData['labels'][] = now()->subDays($i)->format('d/m');
            $chartData['data'][] = $dayData ? $dayData->total : 0;
        }
        $data['chartData'] = $chartData;

        // 4. Estadísticas de Navegador y SO (Aproximación simple)
        $userAgents = PageView::select('user_agent')->get();
        $browsers = ['Chrome' => 0, 'Firefox' => 0, 'Safari' => 0, 'Edge' => 0, 'Otros' => 0];
        $os = ['Windows' => 0, 'Mac' => 0, 'Linux' => 0, 'Android' => 0, 'iOS' => 0, 'Otros' => 0];

        foreach ($userAgents as $ua) {
            $agent = $ua->user_agent;
            if (!$agent)
                continue;

            // Browser
            if (strpos($agent, 'Chrome') !== false)
                $browsers['Chrome']++;
            elseif (strpos($agent, 'Firefox') !== false)
                $browsers['Firefox']++;
            elseif (strpos($agent, 'Safari') !== false)
                $browsers['Safari']++;
            elseif (strpos($agent, 'Edg') !== false)
                $browsers['Edge']++;
            else
                $browsers['Otros']++;

            // OS
            if (strpos($agent, 'Windows') !== false)
                $os['Windows']++;
            elseif (strpos($agent, 'Macintosh') !== false)
                $os['Mac']++;
            elseif (strpos($agent, 'Linux') !== false)
                $os['Linux']++;
            elseif (strpos($agent, 'Android') !== false)
                $os['Android']++;
            elseif (strpos($agent, 'iPhone') !== false || strpos($agent, 'iPad') !== false)
                $os['iOS']++;
            else
                $os['Otros']++;
        }

        $data['browserStats'] = $browsers;
        $data['osStats'] = $os;

        return view('index', $data);
    }
}
