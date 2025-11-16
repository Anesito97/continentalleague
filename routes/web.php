<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; // Nuevo: para cargar todas las vistas
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\NewsAdminController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\GalleryController;

// --- RUTA PÚBLICA / HOME ---
// Esta ruta carga la vista principal, incluyendo clasificación y estadísticas.
Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::get('calendar', [DashboardController::class, 'showCalendar'])->name('matches.calendar');

// Lista de todas las noticias (con paginación)
Route::get('news', [PublicController::class, 'indexNews'])->name('news.index');

// Detalle de una noticia
Route::get('news/{noticia:id}', [PublicController::class, 'showNews'])->name('news.show');

// REglas
Route::get('rules', [DashboardController::class, 'showRules'])->name('rules.index');

Route::get('team/{equipo}', [PublicController::class, 'showTeamProfile'])->name('team.profile');
Route::get('player/{jugador}', [PublicController::class, 'showPlayerProfile'])->name('player.profile');

Route::post('vote/{match_id}', [VoteController::class, 'handleVote'])->name('community.vote');

Route::get('images', [GalleryController::class, 'index'])->name('gallery.index');

// --- AUTENTICACIÓN ---
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTAS DE ADMINISTRACIÓN (Protegidas) ---
// Usaremos un Middleware simple para simular 'is_admin'
Route::middleware(\App\Http\Middleware\AdminCheck::class)
    ->prefix('admin') // ⬅️ AÑADIR ESTE PREFIJO
    ->group(function () {

        //SUBIR IMAGENES
        Route::post('/gallery/upload', [GalleryController::class, 'upload'])->name('gallery.upload');
        //ELIMINAR IMAGENES
        Route::delete('/gallery/{item}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

        // VISTAS DE RENDERIZADO PRINCIPAL (Para la navegación Admin)
        Route::get('teams', [DashboardController::class, 'adminTeams'])->name('admin.teams');
        Route::get('players', [DashboardController::class, 'adminPlayers'])->name('admin.players');
        Route::get('matches', [DashboardController::class, 'adminMatches'])->name('admin.matches');
        Route::get('finalize-match', [DashboardController::class, 'adminFinalizeMatch'])->name('admin.finalize-match');
        Route::get('/', [DashboardController::class, 'adminTeams'])->name('admin.panel');

        // ⬇️ Rutas POST para registro ⬇️
        // CRUD DE EQUIPOS
        Route::post('teams', [TeamController::class, 'store'])->name('teams.store');
        Route::post('players', [PlayerController::class, 'store'])->name('players.store');
        Route::post('matches', [MatchController::class, 'store'])->name('matches.store');
        Route::post('finalize-match', [MatchController::class, 'finalize'])->name('matches.finalize');

        // ⬇️ Rutas GET para mostrar el formulario de edición ⬇️
        Route::get('teams/{equipo}/edit', [DashboardController::class, 'editTeam'])->name('teams.edit');
        Route::get('players/{jugador}/edit', [DashboardController::class, 'editPlayer'])->name('players.edit');
        Route::get('matches/{partido}/edit', [DashboardController::class, 'editMatch'])->name('matches.edit');

        // ⬇️ Rutas PUT para ACTUALIZAR los datos (Usadas por el formulario edit.blade.php) ⬇️
        Route::put('teams/{equipo}', [TeamController::class, 'update'])->name('teams.update');
        Route::put('players/{jugador}', [PlayerController::class, 'update'])->name('players.update');
        Route::put('matches/{partido}', [MatchController::class, 'update'])->name('matches.update');

        // ⬇️ Rutas DELETE para eliminar (Ya estaban definidas) ⬇️
        Route::delete('teams/{equipo}', [TeamController::class, 'destroy'])->name('teams.destroy');
        Route::delete('players/{jugador}', [PlayerController::class, 'destroy'])->name('players.destroy');
        Route::delete('matches/{partido}', [MatchController::class, 'destroy'])->name('matches.destroy');

        Route::get('teams/{equipo}/players', [DashboardController::class, 'showTeamPlayers'])->name('teams.players');

        // RUTA DE NAVEGACIÓN
        Route::get('news', [DashboardController::class, 'adminNews'])->name('admin.news');
        // CRUD DE NOTICIAS
        Route::post('news', [NewsAdminController::class, 'store'])->name('news.store');
        Route::delete('news/{noticia}', [NewsAdminController::class, 'destroy'])->name('news.destroy');

        // RUTAS DE EDICIÓN (Añadir si implementas el edit/update en el controlador)
        Route::get('news/{noticia}/edit', [DashboardController::class, 'editNews'])->name('admin.news.edit');
        // RUTA PUT PARA ACTUALIZAR (Debe existir)
        Route::put('news/{noticia}', [NewsAdminController::class, 'update'])->name('news.update');

        // RUTAS PENDIENTES (Ej: teams/delete/{id}, players/edit/{id}, etc.)
    });