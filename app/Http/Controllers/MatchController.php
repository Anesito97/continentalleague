<?php
namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\EventoPartido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    // 1. Programar Partido (POST desde el formulario)
    public function store(Request $request)
    {
        // Validamos 'date' y 'time' por separado
        $request->validate([
            'localId' => 'required|exists:equipos,id|different:visitorId',
            'visitorId' => 'required|exists:equipos,id',
            'date' => 'required|date', // ⬅️ Usamos date
            'time' => 'required|date_format:H:i', // ⬅️ Usamos time
        ]);

        // Combinamos la fecha y hora en el controlador (limpiando el JS del formulario)
        $dateTime = $request->date . ' ' . $request->time;

        Partido::create([
            'equipo_local_id' => $request->localId,
            'equipo_visitante_id' => $request->visitorId,
            'fecha_hora' => $dateTime, // ⬅️ Usamos la combinación
            'estado' => 'pendiente',
        ]);

        return redirect()->route('admin.matches')->with('success', 'Partido programado correctamente.');
    }

    // 2. Finalizar Partido (Implementación de la transacción de api.php)
    public function finalize(Request $request)
    {
        $request->validate([
            'match_id' => 'required|exists:partidos,id',
            'goles_local' => 'required|integer|min:0',
            'goles_visitor' => 'required|integer|min:0',
            // Los eventos son opcionales
            'events' => 'nullable|array',
        ]);

        $matchId = $request->match_id;
        $golesLocal = $request->goles_local;
        $golesVisitor = $request->goles_visitor;
        $events = $request->events ?? [];

        // --- INICIAR TRANSACCIÓN ---
        DB::beginTransaction();

        try {
            // A. Obtener y verificar el partido
            $match = Partido::where('id', $matchId)
                ->where('estado', 'pendiente')
                ->firstOrFail();

            $localId = $match->equipo_local_id;
            $visitorId = $match->equipo_visitante_id;

            // B. Determinar puntos y stats del partido (la misma lógica que en api.php)
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

            // C. Actualizar Estadísticas de Equipos (USANDO ELOQUENT)
            Equipo::where('id', $localId)->increment('puntos', $localPts);
            Equipo::where('id', $localId)->increment('partidos_jugados', 1);
            Equipo::where('id', $localId)->increment('ganados', $localG);
            Equipo::where('id', $localId)->increment('empatados', $localE);
            Equipo::where('id', $localId)->increment('perdidos', $localP);
            Equipo::where('id', $localId)->increment('goles_a_favor', $golesLocal);
            Equipo::where('id', $localId)->increment('goles_en_contra', $golesVisitor);

            Equipo::where('id', $visitorId)->increment('puntos', $visitorPts);
            Equipo::where('id', $visitorId)->increment('partidos_jugados', 1);
            Equipo::where('id', $visitorId)->increment('ganados', $visitorG);
            Equipo::where('id', $visitorId)->increment('empatados', $visitorE);
            Equipo::where('id', $visitorId)->increment('perdidos', $visitorP);
            Equipo::where('id', $visitorId)->increment('goles_a_favor', $golesVisitor);
            Equipo::where('id', $visitorId)->increment('goles_en_contra', $golesLocal);

            // D. Registrar Eventos y Actualizar Jugadores
            foreach ($events as $event) {
                $playerId = (int) $event['player_id'];
                $eventType = strtolower($event['event_type']);
                $minuto = (int) $event['minuto'];

                if ($playerId > 0) {
                    $player = Jugador::findOrFail($playerId);

                    // 1. Insertar el evento
                    EventoPartido::create([
                        'partido_id' => $matchId,
                        'jugador_id' => $playerId,
                        'equipo_id' => $player->equipo_id,
                        'tipo_evento' => $eventType,
                        'minuto' => $minuto,
                    ]);

                    // 2. Actualizar estadísticas del jugador
                    $statCol = match ($eventType) {
                        'gol' => 'goles',
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

            // E. Finalizar Partido
            $match->update([
                'goles_local' => $golesLocal,
                'goles_visitante' => $golesVisitor,
                'estado' => 'finalizado',
            ]);

            // 3. Confirmar Transacción
            DB::commit();

            return redirect()->route('admin.finalize-match')->with('success', 'Partido finalizado y estadísticas actualizadas con éxito.');

        } catch (\Exception $e) {
            // 4. Revertir Transacción si algo falla
            DB::rollback();
            return back()->with('error', 'Error interno al finalizar el partido: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, Partido $partido)
    {
        $request->validate([
            'localId' => 'required|exists:equipos,id|different:visitorId',
            'visitorId' => 'required|exists:equipos,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $dateTime = $request->date . ' ' . $request->time;

        $partido->update([
            'equipo_local_id' => $request->localId,
            'equipo_visitante_id' => $request->visitorId,
            'fecha_hora' => $dateTime,
        ]);

        return redirect()->route('admin.matches')->with('success', 'Partido actualizado.');
    }

    public function destroy(Partido $partido)
    {
        // Si el partido está finalizado, debes decidir si quieres revertir las estadísticas.
        // Asumiremos que solo se eliminan partidos pendientes o que la eliminación de finalizados es un riesgo que se acepta.
        $partido->delete();
        return redirect()->route('admin.matches')->with('success', 'Partido eliminado.');
    }
}