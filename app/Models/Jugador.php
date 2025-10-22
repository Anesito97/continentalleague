<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    protected $table = 'jugadores';

    protected $fillable = ['equipo_id', 'nombre', 'numero', 'posicion_general', 'posicion_especifica', 'foto_url', 'goles', 'asistencias', 'paradas', 'amarillas', 'rojas'];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function eventos()
    {
        return $this->hasMany(EventoPartido::class, 'jugador_id');
    }
}
