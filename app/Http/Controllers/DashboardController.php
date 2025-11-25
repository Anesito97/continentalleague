<?php

namespace App\Http\Controllers;

use App\Traits\LoadsCommonData;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use LoadsCommonData;

    // 1. VISTA PÚBLICA (HOME/STANDINGS/STATS)
    public function index(Request $request) // ⬅️ Aceptar el objeto Request
    {
        $data = $this->loadAllData();

        // ⬇️ Determinar la vista activa (home por defecto) ⬇️
        $data['news'] = $this->getEmptyNewsPaginator();
        $data['activeView'] = $request->query('view', 'home');

        return view('index', $data);
    }

    public function showRules()
    {
        // Carga solo los datos necesarios para el layout (si no se cargan globalmente)
        $data = $this->loadAllData();
        $data['activeView'] = 'rules'; // Nueva vista activa

        return view('rules', $data); // Usaremos una nueva vista 'rules.blade.php'
    }
}
