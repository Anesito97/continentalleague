<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipos';

    protected $fillable = ['nombre', 'logros_descripcion', 'escudo_url', 'puntos', 'partidos_jugados', 'ganados', 'empatados', 'perdidos', 'goles_a_favor', 'goles_en_contra'];

    public function jugadores()
    {
        return $this->hasMany(Jugador::class);
    }

    public function localMatches()
    {
        return $this->hasMany(Partido::class, 'equipo_local_id');
    }

    public function visitorMatches()
    {
        return $this->hasMany(Partido::class, 'equipo_visitante_id');
    }
}
