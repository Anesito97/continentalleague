<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use App\Models\EventoPartido;
use Carbon\Carbon;

class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. LIMPIAR TABLAS
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Equipo::truncate();
        Jugador::truncate();
        Partido::truncate();
        EventoPartido::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. CREAR EQUIPOS (Solo usando columnas válidas)
        $teamsData = [
            [
                'nombre' => 'Titanes FC',
                'escudo_url' => asset('uploads/logos/logo_titanes.png'),
                'puntos' => 7, 'partidos_jugados' => 3, 'ganados' => 2, 'empatados' => 1, 'perdidos' => 0, 
                'goles_a_favor' => 8, 'goles_en_contra' => 3, // 'clean_sheets' ELIMINADO
            ],
            [
                'nombre' => 'Halcones Rojos',
                'escudo_url' => asset('uploads/logos/logo_halcones.png'),
                'puntos' => 4, 'partidos_jugados' => 3, 'ganados' => 1, 'empatados' => 1, 'perdidos' => 1, 
                'goles_a_favor' => 5, 'goles_en_contra' => 5, // 'clean_sheets' ELIMINADO
            ],
            [
                'nombre' => 'Dragones Azules',
                'escudo_url' => asset('uploads/logos/logo_dragones.png'),
                'puntos' => 3, 'partidos_jugados' => 3, 'ganados' => 1, 'empatados' => 0, 'perdidos' => 2, 
                'goles_a_favor' => 5, 'goles_en_contra' => 7, // 'clean_sheets' ELIMINADO
            ],
            [
                'nombre' => 'Lobos Plateados',
                'escudo_url' => asset('uploads/logos/logo_lobos.png'),
                'puntos' => 3, 'partidos_jugados' => 3, 'ganados' => 1, 'empatados' => 0, 'perdidos' => 2, 
                'goles_a_favor' => 3, 'goles_en_contra' => 6, // 'clean_sheets' ELIMINADO
            ],
        ];

        $teams = [];
        foreach ($teamsData as $data) {
            $teams[] = Equipo::create($data);
        }

        // Acceder a los modelos creados
        $equipoTitanes = $teams[0];
        $equipoHalcones = $teams[1];
        $equipoDragones = $teams[2];
        $equipoLobos = $teams[3];
        
        $localUploadPath = 'uploads/player_photos/'; 

        // 3. CREAR JUGADORES (Solo usando columnas válidas)
        $playersData = [
            // Titanes FC (Id: 1)
            ['equipo_id' => $equipoTitanes->id, 'nombre' => 'Marco Goleador', 'numero' => 10, 'posicion' => 'delantero', 'goles' => 5, 'asistencias' => 1, 'foto_url' => asset($localUploadPath . 'player_marco.png')],
            ['equipo_id' => $equipoTitanes->id, 'nombre' => 'Lucas Asistente', 'numero' => 7, 'posicion' => 'medio', 'goles' => 1, 'asistencias' => 3, 'foto_url' => asset($localUploadPath . 'player_lucas.png')],
            ['equipo_id' => $equipoTitanes->id, 'nombre' => 'David Muro', 'numero' => 4, 'posicion' => 'defensa', 'goles' => 0, 'asistencias' => 0],
            ['equipo_id' => $equipoTitanes->id, 'nombre' => 'Alex Portero', 'numero' => 1, 'posicion' => 'portero', 'paradas' => 8], // paradas es válida
            
            // Halcones Rojos (Id: 2)
            ['equipo_id' => $equipoHalcones->id, 'nombre' => 'Leo Máquina', 'numero' => 9, 'posicion' => 'delantero', 'goles' => 3, 'asistencias' => 0],
            ['equipo_id' => $equipoHalcones->id, 'nombre' => 'Sofía Veloz', 'numero' => 11, 'posicion' => 'medio', 'goles' => 1, 'asistencias' => 1],
            ['equipo_id' => $equipoHalcones->id, 'nombre' => 'Carlos Def', 'numero' => 3, 'posicion' => 'defensa', 'rojas' => 1], // rojas es válida
            ['equipo_id' => $equipoHalcones->id, 'nombre' => 'Javi Paradas', 'numero' => 13, 'posicion' => 'portero', 'paradas' => 10],

            // Dragones Azules (Id: 3)
            ['equipo_id' => $equipoDragones->id, 'nombre' => 'Nico Tirador', 'numero' => 17, 'posicion' => 'delantero', 'goles' => 2, 'amarillas' => 2], // amarillas es válida
            ['equipo_id' => $equipoDragones->id, 'nombre' => 'Eva Creadora', 'numero' => 8, 'posicion' => 'medio', 'goles' => 1, 'asistencias' => 1],
            ['equipo_id' => $equipoDragones->id, 'nombre' => 'Raúl Fuerte', 'numero' => 5, 'posicion' => 'defensa'],
            ['equipo_id' => $equipoDragones->id, 'nombre' => 'Marta Guardiana', 'numero' => 1, 'posicion' => 'portero', 'paradas' => 5],
            
            // Lobos Plateados (Id: 4)
            ['equipo_id' => $equipoLobos->id, 'nombre' => 'Pablo Rápido', 'numero' => 15, 'posicion' => 'medio', 'goles' => 2, 'asistencias' => 1],
            ['equipo_id' => $equipoLobos->id, 'nombre' => 'Elena Fina', 'numero' => 2, 'posicion' => 'defensa', 'goles' => 1],
            ['equipo_id' => $equipoLobos->id, 'nombre' => 'Toni Bloqueo', 'numero' => 6, 'posicion' => 'defensa'],
            ['equipo_id' => $equipoLobos->id, 'nombre' => 'Laura Portera', 'numero' => 1, 'posicion' => 'portero', 'paradas' => 7],
        ];

        foreach ($playersData as $data) {
            Jugador::create($data);
        }
        
        // 4. CREAR PARTIDOS Y EVENTOS (Usando datos válidos)
        $jugadorMarco = Jugador::where('nombre', 'Marco Goleador')->first();
        $jugadorLucas = Jugador::where('nombre', 'Lucas Asistente')->first();
        $jugadorLeo = Jugador::where('nombre', 'Leo Máquina')->first();
        $jugadorAlex = Jugador::where('nombre', 'Alex Portero')->first();
        $jugadorNico = Jugador::where('nombre', 'Nico Tirador')->first();
        $jugadorPablo = Jugador::where('nombre', 'Pablo Rápido')->first();

        // PARTIDO 1 (FINALIZADO: 3-3)
        $match1 = Partido::create([
            'equipo_local_id' => $equipoTitanes->id, 
            'equipo_visitante_id' => $equipoHalcones->id, 
            'fecha_hora' => Carbon::now()->subDays(7),
            'estado' => 'finalizado', 'goles_local' => 3, 'goles_visitante' => 3
        ]);
        EventoPartido::create(['partido_id' => $match1->id, 'jugador_id' => $jugadorLeo->id, 'equipo_id' => $equipoHalcones->id, 'tipo_evento' => 'gol', 'minuto' => 15]);
        EventoPartido::create(['partido_id' => $match1->id, 'jugador_id' => $jugadorMarco->id, 'equipo_id' => $equipoTitanes->id, 'tipo_evento' => 'gol', 'minuto' => 25]);
        EventoPartido::create(['partido_id' => $match1->id, 'jugador_id' => $jugadorMarco->id, 'equipo_id' => $equipoTitanes->id, 'tipo_evento' => 'gol', 'minuto' => 30]);
        EventoPartido::create(['partido_id' => $match1->id, 'jugador_id' => $jugadorLeo->id, 'equipo_id' => $equipoHalcones->id, 'tipo_evento' => 'gol', 'minuto' => 50]);
        EventoPartido::create(['partido_id' => $match1->id, 'jugador_id' => $jugadorLeo->id, 'equipo_id' => $equipoHalcones->id, 'tipo_evento' => 'asistencia', 'minuto' => 50]);
        EventoPartido::create(['partido_id' => $match1->id, 'jugador_id' => $jugadorLucas->id, 'equipo_id' => $equipoTitanes->id, 'tipo_evento' => 'gol', 'minuto' => 75]);
        
        // PARTIDO 2 (FINALIZADO: 0-2)
        $match2 = Partido::create([
            'equipo_local_id' => $equipoLobos->id, 
            'equipo_visitante_id' => $equipoDragones->id, 
            'fecha_hora' => Carbon::now()->subDays(5),
            'estado' => 'finalizado', 'goles_local' => 0, 'goles_visitante' => 2
        ]);
        EventoPartido::create(['partido_id' => $match2->id, 'jugador_id' => $jugadorNico->id, 'equipo_id' => $equipoDragones->id, 'tipo_evento' => 'gol', 'minuto' => 40]);
        EventoPartido::create(['partido_id' => $match2->id, 'jugador_id' => $jugadorNico->id, 'equipo_id' => $equipoDragones->id, 'tipo_evento' => 'gol', 'minuto' => 60]);
        
        // PARTIDO 3 (FINALIZADO: 5-0)
        $match3 = Partido::create([
            'equipo_local_id' => $equipoTitanes->id, 
            'equipo_visitante_id' => $equipoLobos->id, 
            'fecha_hora' => Carbon::now()->subDays(2),
            'estado' => 'finalizado', 'goles_local' => 5, 'goles_visitante' => 0
        ]);
        EventoPartido::create(['partido_id' => $match3->id, 'jugador_id' => $jugadorMarco->id, 'equipo_id' => $equipoTitanes->id, 'tipo_evento' => 'gol', 'minuto' => 10]);
        EventoPartido::create(['partido_id' => $match3->id, 'jugador_id' => $jugadorMarco->id, 'equipo_id' => $equipoTitanes->id, 'tipo_evento' => 'asistencia', 'minuto' => 10]);
        EventoPartido::create(['partido_id' => $match3->id, 'jugador_id' => $jugadorMarco->id, 'equipo_id' => $equipoTitanes->id, 'tipo_evento' => 'gol', 'minuto' => 20]);
        EventoPartido::create(['partido_id' => $match3->id, 'jugador_id' => $jugadorLucas->id, 'equipo_id' => $equipoTitanes->id, 'tipo_evento' => 'gol', 'minuto' => 45]);
        EventoPartido::create(['partido_id' => $match3->id, 'jugador_id' => $jugadorPablo->id, 'equipo_id' => $equipoLobos->id, 'tipo_evento' => 'roja', 'minuto' => 60]);

        // PARTIDO 4 (PENDIENTE)
        Partido::create([
            'equipo_local_id' => $equipoHalcones->id, 
            'equipo_visitante_id' => $equipoDragones->id, 
            'fecha_hora' => Carbon::now()->addDays(2)->setTime(20, 0),
            'estado' => 'pendiente'
        ]);

        // PARTIDO 5 (PENDIENTE)
        Partido::create([
            'equipo_local_id' => $equipoLobos->id, 
            'equipo_visitante_id' => $equipoTitanes->id, 
            'fecha_hora' => Carbon::now()->addDays(5)->setTime(18, 30),
            'estado' => 'pendiente'
        ]);

                // 5. CREAR NOTICIAS (Para el Slider)
        
        // Define una URL de imagen genérica para el banner
        $newsImageUrl = asset('uploads/news/banner_default.jpg'); 

        DB::table('noticias')->insert([
            [
                'titulo' => '¡Titanes FC en Racha! Imparables en la Jornada 3',
                'contenido' => 'El Titanes FC demostró su poder ofensivo al golear 5-0 al Lobos Plateados, afianzándose en la cima de la tabla. Marco Goleador anotó un doblete y lidera la tabla de goleadores.',
                'imagen_url' => $newsImageUrl,
                'publicada_en' => Carbon::now()->subHours(12),
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'titulo' => 'Duelo en la Cumbre: Halcones vs. Dragones, ¿quién dominará?',
                'contenido' => 'El próximo partido entre Halcones Rojos y Dragones Azules será crucial para definir la mitad superior de la tabla. Ambos equipos buscan asegurar un puesto de honor.',
                'imagen_url' => $newsImageUrl,
                'publicada_en' => Carbon::now()->subHours(8),
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'titulo' => 'Leo Máquina Brilla con un Hat-trick a pesar del Empate',
                'contenido' => 'Leo Máquina (Halcones Rojos) tuvo una actuación estelar con tres goles, pero no fue suficiente para asegurar la victoria contra Titanes FC en el emocionante empate 3-3.',
                'imagen_url' => $newsImageUrl,
                'publicada_en' => Carbon::now()->subDays(1),
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'titulo' => 'Dragones Azules se recuperan con una victoria clave de visitante',
                'contenido' => 'Nico Tirador marcó los dos goles en la victoria 2-0 de los Dragones Azules sobre Lobos Plateados, mostrando solidez defensiva.',
                'imagen_url' => $newsImageUrl,
                'publicada_en' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
            [
                'titulo' => '¡Lobos y Titanes se enfrentarán en la Jornada 5!',
                'contenido' => 'Uno de los duelos más esperados de la temporada regular se aproxima. Analizamos las claves del partido que enfrentará a Lobos Plateados contra el líder Titanes FC.',
                'imagen_url' => $newsImageUrl,
                'publicada_en' => Carbon::now()->subDays(3),
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
            ],
        ]);
    }
}