<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController; // Nuevo: para cargar todas las vistas
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\NewsAdminController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// --- RUTA PÚBLICA / HOME ---
// Esta ruta carga la vista principal, incluyendo clasificación y estadísticas.
Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::get('calendar', [MatchController::class, 'showCalendar'])->name('matches.calendar');

// Lista de todas las noticias (con paginación)
Route::get('news', [PublicController::class, 'indexNews'])->name('news.index');

// Detalle de una noticia
Route::get('news/{noticia:id}', [PublicController::class, 'showNews'])->name('news.show');

// REglas
Route::get('rules', [DashboardController::class, 'showRules'])->name('rules.index');

Route::get('team/{equipo}', [PublicController::class, 'showTeamProfile'])->name('team.profile');
Route::get('player/{jugador}', [PublicController::class, 'showPlayerProfile'])->name('player.profile');

Route::post('vote/{match_id}', [VoteController::class, 'handleVote'])->name('community.vote');

Route::get('imagess', [GalleryController::class, 'index'])->name('gallery.index');

// --- AUTENTICACIÓN ---
Route::get('login', function () {
    return view('auth.login');
})->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// Google Auth
Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::get('/ideal-11', [App\Http\Controllers\IdealElevenController::class, 'index'])->name('ideal-eleven');
Route::get('/mvp', [App\Http\Controllers\MvpController::class, 'index'])->name('mvp.index');

// --- LINEUP BUILDER ---
Route::get('/lineup-builder', [\App\Http\Controllers\LineupBuilderController::class, 'index'])->name('lineup.index');
Route::get('/lineup-builder/players/{team}', [\App\Http\Controllers\LineupBuilderController::class, 'getPlayers'])->name('lineup.players');
Route::get('/lineup-builder/proxy', [\App\Http\Controllers\LineupBuilderController::class, 'proxyImage'])->name('lineup.proxy');

// --- MINIJUEGOS ---
Route::middleware('auth')->group(function () {
    Route::get('/games', [\App\Http\Controllers\GameController::class, 'index'])->name('games.index');
    Route::get('/games/keepy-uppy', [\App\Http\Controllers\GameController::class, 'keepyUppy'])->name('game.keepy-uppy');
    Route::get('/games/penalty', [\App\Http\Controllers\GameController::class, 'penalty'])->name('game.penalty');
    Route::get('/games/portero-runner', [\App\Http\Controllers\GameController::class, 'porteroRunner'])->name('game.portero-runner');
    Route::post('/game/save', [\App\Http\Controllers\GameController::class, 'store'])->name('game.save');
});

// --- RUTAS DE ADMINISTRACIÓN (Protegidas) ---
// Usaremos un Middleware simple para simular 'is_admin'
Route::middleware(\App\Http\Middleware\AdminCheck::class)
    ->prefix('admin') // ⬅️ AÑADIR ESTE PREFIJO
    ->group(function () {

        // SUBIR IMAGENES
        Route::post('/gallery/upload', [GalleryController::class, 'upload'])->name('gallery.upload');
        // ELIMINAR IMAGENES
        Route::delete('/gallery/{item}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

        // VISTAS DE RENDERIZADO PRINCIPAL (Para la navegación Admin)
        Route::get('teams', [TeamController::class, 'adminTeams'])->name('admin.teams');
        Route::get('players', [PlayerController::class, 'adminPlayers'])->name('admin.players');
        Route::get('matches', [MatchController::class, 'adminMatches'])->name('admin.matches');
        Route::get('finalize-match', [MatchController::class, 'adminFinalizeMatch'])->name('admin.finalize-match');
        Route::get('/', [TeamController::class, 'adminTeams'])->name('admin.panel');

        // ⬇️ Rutas POST para registro ⬇️
        // CRUD DE EQUIPOS
        Route::post('teams', [TeamController::class, 'store'])->name('teams.store');
        Route::post('players', [PlayerController::class, 'store'])->name('players.store');
        Route::post('matches', [MatchController::class, 'store'])->name('matches.store');
        Route::post('finalize-match', [MatchController::class, 'finalize'])->name('matches.finalize');

        // ⬇️ Rutas GET para mostrar el formulario de edición ⬇️
        Route::get('teams/{equipo}/edit', [TeamController::class, 'editTeam'])->name('teams.edit');
        Route::get('players/{jugador}/edit', [PlayerController::class, 'editPlayer'])->name('players.edit');
        Route::get('matches/{partido}/edit', [MatchController::class, 'editMatch'])->name('matches.edit');

        // ⬇️ Rutas PUT para ACTUALIZAR los datos (Usadas por el formulario edit.blade.php) ⬇️
        Route::put('teams/{equipo}', [TeamController::class, 'update'])->name('teams.update');
        Route::put('players/{jugador}', [PlayerController::class, 'update'])->name('players.update');
        Route::put('matches/{partido}', [MatchController::class, 'update'])->name('matches.update');

        // ⬇️ Rutas DELETE para eliminar (Ya estaban definidas) ⬇️
        Route::delete('teams/{equipo}', [TeamController::class, 'destroy'])->name('teams.destroy');
        Route::delete('players/{jugador}', [PlayerController::class, 'destroy'])->name('players.destroy');
        Route::delete('matches/{partido}', [MatchController::class, 'destroy'])->name('matches.destroy');

        Route::get('teams/{equipo}/players', [TeamController::class, 'showTeamPlayers'])->name('teams.players');

        // RUTA DE NAVEGACIÓN
        Route::get('news', [NewsAdminController::class, 'adminNews'])->name('admin.news');
        // CRUD DE NOTICIAS
        Route::post('news', [NewsAdminController::class, 'store'])->name('news.store');
        Route::delete('news/{noticia}', [NewsAdminController::class, 'destroy'])->name('news.destroy');

        // RUTAS DE EDICIÓN (Añadir si implementas el edit/update en el controlador)
        Route::get('news/{noticia}/edit', [NewsAdminController::class, 'editNews'])->name('admin.news.edit');
        // RUTA PUT PARA ACTUALIZAR (Debe existir)
        Route::put('news/{noticia}', [NewsAdminController::class, 'update'])->name('news.update');

        // --- DEEP ANALYSIS (NUEVO) ---
        Route::get('deep-analysis', [\App\Http\Controllers\DeepAnalysisController::class, 'index'])->name('admin.analysis.index');
        Route::post('deep-analysis/selection', [\App\Http\Controllers\DeepAnalysisController::class, 'selection'])->name('admin.analysis.selection');
        Route::post('deep-analysis', [\App\Http\Controllers\DeepAnalysisController::class, 'analyze'])->name('admin.analysis.analyze');
        Route::get('/api/teams/{id}/players', function ($id) {
            return \App\Models\Jugador::where('equipo_id', $id)->orderBy('nombre')->get(['id', 'nombre']);
        });

        // --- NOTIFICACIONES PREDEFINIDAS ---
        Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::post('notifications', [\App\Http\Controllers\NotificationController::class, 'store'])->name('admin.notifications.store');
        Route::put('notifications/{message}', [\App\Http\Controllers\NotificationController::class, 'update'])->name('admin.notifications.update');
        Route::delete('notifications/{message}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
        Route::post('notifications/{message}/send', [\App\Http\Controllers\NotificationController::class, 'send'])->name('admin.notifications.send');

        // RUTAS PENDIENTES (Ej: teams/delete/{id}, players/edit/{id}, etc.)
    });

// --- RUTA DE PRUEBA WHATSAPP ---
Route::get('/test-whatsapp', function (\App\Services\WhatsAppService $whatsapp) {
    $success = $whatsapp->sendMessage('Notificación de prueba desde el BOT🚀');
    return $success ? 'Mensaje enviado correctamente' : 'Error al enviar mensaje';
});
