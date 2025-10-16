<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Noticia;
use App\Models\Equipo; // Necesario para loadAllData si lo mueves aquí

class PublicController extends Controller
{
    // Método para listar todas las noticias con paginación
    public function indexNews()
    {
        // Obtener las noticias, ordenadas por fecha de publicación, 10 por página
        $news = Noticia::orderBy('publicada_en', 'desc')->paginate(10);

        // Puedes llamar a loadAllData() si necesitas los tops/teams/etc. en el layout,
        // pero aquí solo pasaremos lo esencial para la vista de noticias.
        $teams = Equipo::all(); // Carga de datos para el layout si es necesario

        return view('news.index', compact('news', 'teams'));
    }

    // Método para mostrar el detalle de una sola noticia
    public function showNews(Noticia $noticia)
    {
        $teams = Equipo::all(); // Carga de datos para el layout
        
        return view('news.show', compact('noticia', 'teams'));
    }
}