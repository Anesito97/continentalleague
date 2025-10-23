<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoPartido extends Model
{
    use HasFactory;
    
    // Nombre de la tabla
    protected $table = 'eventos_partido'; 

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'partido_id',
        'jugador_id',
        'equipo_id',
        'tipo_evento',
        'goal_type',
        'minuto',
    ];

    // RELACIONES ELOQUENT

    /**
     * Obtiene el partido al que pertenece el evento.
     */
    public function partido()
    {
        return $this->belongsTo(Partido::class, 'partido_id');
    }

    /**
     * Obtiene el jugador que realizó el evento.
     */
    public function jugador()
    {
        return $this->belongsTo(Jugador::class, 'jugador_id');
    }

    /**
     * Obtiene el equipo al que pertenecía el jugador en el momento del evento.
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }
}