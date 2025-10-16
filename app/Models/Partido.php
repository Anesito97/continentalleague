<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'partidos';

    // Campos que pueden ser asignados masivamente (para creación y actualización)
    protected $fillable = [
        'equipo_local_id',
        'equipo_visitante_id',
        'jornada',
        'fecha_hora',
        'estado',
        'goles_local',
        'goles_visitante',
    ];

    // Castear la columna fecha_hora a un objeto Carbon (opcional pero recomendado)
    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    // RELACIONES ELOQUENT

    /**
     * Obtiene el equipo local del partido.
     */
    public function localTeam()
    {
        // La clave foránea es equipo_local_id
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    /**
     * Obtiene el equipo visitante del partido.
     */
    public function visitorTeam()
    {
        // La clave foránea es equipo_visitante_id
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }

    /**
     * Obtiene todos los eventos asociados a este partido.
     */
    public function eventos()
    {
        return $this->hasMany(EventoPartido::class, 'partido_id');
    }
}