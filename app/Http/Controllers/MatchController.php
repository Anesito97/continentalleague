<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\EventoPartido;
use App\Models\Jugador;
use App\Models\Partido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Traits\LoadsCommonData;

class MatchController extends Controller
{
    use LoadsCommonData;

    public function adminMatches()
    {
        session(['activeAdminContent' => 'matches']);
        $data = $this->loadAllData();
        $data['news'] = $this->getEmptyNewsPaginator();
        $data['activeView'] = 'admin';

        return view('index', $data);
    }

    public function adminFinalizeMatch()
    {
        session(['activeAdminContent' => 'finalize-match']);
        $data = $this->loadAllData();
        $data['news'] = $this->getEmptyNewsPaginator();
        $data['activeView'] = 'admin';

        return view('index', $data);
    }

    public function editMatch(Partido $partido)
    {
        $data = $this->loadAllData();
        $data['item'] = $partido;
        $data['type'] = 'match';
        // Asegúrate de cargar las relaciones necesarias para el formulario de partido
        $partido->load(['localTeam', 'visitorTeam']);

        return view('edit', $data);
    }

    public function showCalendar(Request $request)
    {
        // 1. Determinar el filtro activo
        $filter = $request->query('status', 'pending');

        // ⬇️ Obtener el número de jornada activa si se pasa en la URL, o la jornada más baja pendiente ⬇️
        $activeJornada = $request->query('jornada');

        $query = Partido::with(['localTeam', 'visitorTeam', 'eventos.jugador', 'eventos.equipo'])
            ->orderBy('jornada', 'asc') // ⬅️ Ordenar primero por jornada
            ->orderBy('fecha_hora', 'asc');

        // 2. Aplicar filtros de ESTADO (pending, finished, all)
        if ($filter === 'pending') {
            $query->where('estado', 'pendiente');
        } elseif ($filter === 'finished') {
            $query->where('estado', 'finalizado');
        }

        // 3. Aplicar filtro de JORNADA si se selecciona una específica
        if ($activeJornada) {
            $query->where('jornada', $activeJornada);
        }

        $allMatches = $query->get();

        // 4. Agrupar los partidos por JORNADA (CRÍTICO)
        // Ya no agrupamos por fecha, sino por el número de jornada.
        $matchesByJornada = $allMatches->groupBy('jornada');

        // 5. Determinar todas las jornadas existentes para el selector
        $allJornadas = Partido::select('jornada')->distinct()->orderBy('jornada', 'asc')->pluck('jornada');
        // Determinar la jornada más baja que contiene partidos pendientes (para el foco inicial)
        $firstPendingJornada = Partido::where('estado', 'pendiente')->min('jornada');

        // Si no hay jornada activa en la URL, usar la primera pendiente
        if (!$activeJornada && $firstPendingJornada) {
            $activeJornada = $firstPendingJornada;
        } elseif (!$activeJornada) {
            $activeJornada = $allJornadas->first(); // Si no hay pendientes, usar la primera jornada existente
        }

        $data = $this->loadAllData();
        $data['matchesByJornada'] = $matchesByJornada; // ⬅️ Nuevo nombre
        $data['activeView'] = 'calendar';
        $data['activeFilter'] = $filter;
        $data['allJornadas'] = $allJornadas; // Para el selector
        $data['activeJornada'] = (int) $activeJornada; // Para saber qué pestaña resaltar

        return view('calendar', $data);
    }
    // 1. Programar Partido (SIN CAMBIOS)
    public function store(Request $request)
    {
        $request->validate([
            'localId' => 'required|exists:equipos,id|different:visitorId',
            'visitorId' => 'required|exists:equipos,id',
            'jornada' => 'required|integer|min:1',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);
        $dateTime = $request->date . ' ' . $request->time;
        Partido::create([
            'equipo_local_id' => $request->localId,
            'equipo_visitante_id' => $request->visitorId,
            'jornada' => $request->jornada,
            'fecha_hora' => $dateTime,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('admin.matches')->with('success', 'Partido programado correctamente.');
    }

    // 2. Finalizar Partido (Ligeramente modificado para usar las nuevas funciones)
    public function finalize(Request $request)
    {
        $request->validate([
            'match_id' => 'required|exists:partidos,id',
            'goles_local' => 'required|integer|min:0',
            'goles_visitor' => 'required|integer|min:0',
            'events' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $match = Partido::where('id', $request->match_id)
                ->where('estado', 'pendiente')
                ->firstOrFail();

            // A. Actualizar el partido
            $match->update([
                'goles_local' => $request->goles_local,
                'goles_visitante' => $request->goles_visitor,
                'estado' => 'finalizado',
            ]);

            // B. Aplicar estadísticas de equipos y jugadores
            $this->applyTeamStats($match, $request->goles_local, $request->goles_visitor);
            $this->applyPlayerStats($request->events ?? [], $match->id);

            DB::commit();

            return redirect()->route('admin.finalize-match')->with('success', 'Partido finalizado con éxito.');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Error al finalizar el partido: ' . $e->getMessage())->withInput();
        }
    }

    // 3. ACTUALIZAR PARTIDO (¡LA LÓGICA CLAVE!)
    public function update(Request $request, Partido $partido)
    {
        // CASO 1: El partido AÚN ESTÁ PENDIENTE
        // Solo actualizamos la información básica (fecha, equipos, etc.)
        if ($partido->estado === 'pendiente') {
            $request->validate([
                'localId' => 'required|exists:equipos,id|different:visitorId',
                'visitorId' => 'required|exists:equipos,id',
                'jornada' => 'required|integer|min:1',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
            ]);
            $dateTime = $request->date . ' ' . $request->time;
            $partido->update([
                'equipo_local_id' => $request->localId,
                'equipo_visitante_id' => $request->visitorId,
                'jornada' => $request->jornada,
                'fecha_hora' => $dateTime,
            ]);

            return redirect()->route('admin.matches')->with('success', 'Partido pendiente actualizado.');
        }

        // CASO 2: El partido YA ESTÁ FINALIZADO
        // Esta es la lógica de "Re-finalizar": Revertir todo y aplicar lo nuevo.
        $request->validate([
            'goles_local' => 'required|integer|min:0',
            'goles_visitor' => 'required|integer|min:0',
            'events' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // 1. REVERTIR todas las estadísticas antiguas
            $this->reverseStats($partido);

            // 2. ACTUALIZAR el marcador del partido con los nuevos datos
            $partido->update([
                'goles_local' => $request->goles_local,
                'goles_visitante' => $request->goles_visitor,
                // El estado ya es 'finalizado', no se toca
            ]);

            // 3. APLICAR las nuevas estadísticas de equipo
            $this->applyTeamStats($partido, $request->goles_local, $request->goles_visitor);

            // 4. CREAR los nuevos eventos y aplicar estadísticas de jugador
            // (El 'reverseStats' ya borró los eventos antiguos)
            $this->applyPlayerStats($request->events ?? [], $partido->id);

            DB::commit();

            return redirect()->route('admin.matches')->with('success', 'Partido finalizado actualizado con éxito.');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Error al actualizar el partido: ' . $e->getMessage())->withInput();
        }
    }

    // 4. Eliminar Partido (SIN CAMBIOS)
    public function destroy(Partido $partido)
    {
        if ($partido->estado === 'finalizado') {
            // ¡Importante! Si eliminas un partido finalizado, también debes revertir las stats.
            $this->reverseStats($partido);
        }
        $partido->delete();

        return redirect()->route('admin.matches')->with('success', 'Partido eliminado.');
    }

    // ===================================================================
    // FUNCIONES PRIVADAS DE ESTADÍSTICAS (El motor de tu controlador)
    // ===================================================================

    /**
     * APLICA estadísticas a equipos y jugadores.
     */
    private function applyTeamStats(Partido $match, $golesLocal, $golesVisitor)
    {
        $localTeam = $match->localTeam;
        $visitorTeam = $match->visitorTeam;

        $localPts = $localG = $localE = $localP = 0;
        $visitorPts = $visitorG = $visitorE = $visitorP = 0;

        if ($golesLocal > $golesVisitor) {
            $localPts = 3;
            $localG = 1;
            $visitorP = 1;
        } elseif ($golesLocal < $golesVisitor) {
            $visitorPts = 3;
            $visitorG = 1;
            $localP = 1;
        } else {
            $localPts = 1;
            $localE = 1;
            $visitorPts = 1;
            $visitorE = 1;
        }

        $localTeam->increment('puntos', $localPts);
        $localTeam->increment('partidos_jugados', 1);
        $localTeam->increment('ganados', $localG);
        $localTeam->increment('empatados', $localE);
        $localTeam->increment('perdidos', $localP);
        $localTeam->increment('goles_a_favor', $golesLocal);
        $localTeam->increment('goles_en_contra', $golesVisitor);

        $visitorTeam->increment('puntos', $visitorPts);
        $visitorTeam->increment('partidos_jugados', 1);
        $visitorTeam->increment('ganados', $visitorG);
        $visitorTeam->increment('empatados', $visitorE);
        $visitorTeam->increment('perdidos', $visitorP);
        $visitorTeam->increment('goles_a_favor', $golesVisitor);
        $visitorTeam->increment('goles_en_contra', $golesLocal);
    }

    private function applyPlayerStats($events, $matchId)
    {
        foreach ($events as $event) {
            $playerId = (int) ($event['player_id'] ?? 0);
            $eventType = strtolower($event['event_type'] ?? '');

            if ($playerId > 0 && !empty($eventType)) {
                $player = Jugador::find($playerId);
                if (!$player) {
                    continue;
                }

                EventoPartido::create([
                    'partido_id' => $matchId,
                    'jugador_id' => $playerId,
                    'equipo_id' => $player->equipo_id,
                    'tipo_evento' => $eventType,
                    'goal_type' => strtolower($event['goal_type'] ?? null),
                    'minuto' => (int) ($event['minuto'] ?? 0),
                ]);

                $statCol = match ($eventType) {
                    'gol' => (strtolower($event['goal_type'] ?? '') !== 'en contra') ? 'goles' : null,
                    'asistencia' => 'asistencias',
                    'parada' => 'paradas',
                    'amarilla' => 'amarillas',
                    'roja' => 'rojas',
                    default => null,
                };
                if ($statCol) {
                    $player->increment($statCol);
                }
            }
        }
    }

    /**
     * REVIERTE todas las estadísticas de un partido finalizado.
     */
    private function reverseStats(Partido $match)
    {
        // Cargar las relaciones es crucial
        $match->load('eventos.jugador', 'localTeam', 'visitorTeam');

        $localTeam = $match->localTeam;
        $visitorTeam = $match->visitorTeam;

        // 1. Revertir Stats de Equipos
        $localPts = $localG = $localE = $localP = 0;
        $visitorPts = $visitorG = $visitorE = $visitorP = 0;

        if ($match->goles_local > $match->goles_visitante) {
            $localPts = 3;
            $localG = 1;
            $visitorP = 1;
        } elseif ($match->goles_local < $match->goles_visitante) {
            $visitorPts = 3;
            $visitorG = 1;
            $localP = 1;
        } else {
            $localPts = 1;
            $localE = 1;
            $visitorPts = 1;
            $visitorE = 1;
        }

        $localTeam->decrement('puntos', $localPts);
        $localTeam->decrement('partidos_jugados', 1);
        $localTeam->decrement('ganados', $localG);
        $localTeam->decrement('empatados', $localE);
        $localTeam->decrement('perdidos', $localP);
        $localTeam->decrement('goles_a_favor', $match->goles_local);
        $localTeam->decrement('goles_en_contra', $match->goles_visitante);

        $visitorTeam->decrement('puntos', $visitorPts);
        $visitorTeam->decrement('partidos_jugados', 1);
        $visitorTeam->decrement('ganados', $visitorG);
        $visitorTeam->decrement('empatados', $visitorE);
        $visitorTeam->decrement('perdidos', $visitorP);
        $visitorTeam->decrement('goles_a_favor', $match->goles_visitante);
        $visitorTeam->decrement('goles_en_contra', $match->goles_local);

        // 2. Revertir Stats de Jugadores
        foreach ($match->eventos as $event) {
            if ($event->jugador) {
                $statCol = match ($event->tipo_evento) {
                    'gol' => (strtolower($event->goal_type ?? '') !== 'en contra') ? 'goles' : null,
                    'asistencia' => 'asistencias',
                    'parada' => 'paradas',
                    'amarilla' => 'amarillas',
                    'roja' => 'rojas',
                    default => null,
                };
                if ($statCol) {
                    $event->jugador->decrement($statCol);
                }
            }
        }

        // 3. Borrar eventos antiguos
        $match->eventos()->delete();
    }
}
