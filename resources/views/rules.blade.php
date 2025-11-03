@extends('index') 

@section('content')
    <div class="max-w-4xl mx-auto py-8 w-full">
        <h2 class="text-4xl font-extrabold text-white mb-6 border-b border-primary pb-3 flex items-center">
            <span class="material-symbols-outlined mr-3 text-4xl text-primary">gavel</span>
            Reglamento Oficial de la Continental League
        </h2>
        
        <div class="card p-6 shadow-2xl space-y-8">

            {{-- 1. SECCIÓN: ARBITRAJE, TERRENO Y DISCIPLINA --}}
            <div class="space-y-4">
                <h3 class="text-2xl font-bold text-red-400 border-b border-gray-700 pb-1">1. Arbitraje, Terreno y Conducta en el Juego</h3>
                <ul class="list-disc list-inside space-y-3 text-gray-300 ml-4">
                    <li>El equipo visitante no puede llevar árbitros, el árbitro va a ser el de cada terreno y hay que acatar las leyes que el implante. (De ponerse de acuerdo ambos equipos y quieren llevar 2 árbitros y así lo pactan esta bien.)</li>
                    
                    <li>Queda totalmente prohibido discutir con el árbitro, todo lo que el árbitro vea es lo que va a pitar no es profesional. Solamente pueden acercarse él los capitanes siempre con una postura correcta y nunca a reclamar de mala forma o faltar de respeto.</li>

                    <li>( TODOS SOMOS HERMANOS ). El jugador que le falte el respeto a un árbitro, al jugador contrario o incluso a uno de su propio equipo se analizará y puede ser retirado del torneo inmediatamente. (Por mucho que hablen con los principales del torneo, no va a entrar de nuevo). Una falta así no solo es feo si no que mancha el nombre de Jehová. Recordemos que somos apacible y esto es para diversión.</li>

                    <li>De más está decir como es el comportamiento de los jugadores. Equipo que se comporte mal, que el juego contra ellos sea engorroso ya sea porque discuten mucho u incluso entre ellos mismos automáticamente estará fuera del torneo.</li>

                    {{-- <li>Los cambios son libres, cada árbitro va a designar una zona por la que deben hacer los cambios, esto ya depende del árbitro, si lo considera necesario o no.</li> --}}
                    <li>Los cambios son libres. Cuando se va a hacer un cambio el balón tiene que estar detenido y se hace por el medio del terreno, el jugador que sale lo puede hacer por cualquier lado del terreno o por el centro al igual que el jugador que entra, y hasta que no se finalice el cambio y árbitro indique, no se reinicia el partido.</li>
                </ul>
            </div>

            {{-- 2. SECCIÓN: SANCIONES, PACTOS ILEGALES Y REPORTE DE DATOS --}}
            <div class="space-y-4">
                <h3 class="text-2xl font-bold text-red-400 border-b border-gray-700 pb-1">2. Sanciones Disciplinarias y Reporte de Datos</h3>
                <ul class="list-disc list-inside space-y-3 text-gray-300 ml-4">
                    <li>**Tarjeta Roja:** Jugador con tarjeta roja, se pierde el próximo encuentro. No se puede pactar entre equipos que el jugador juegue, si ambos equipos pactan para que juegue automáticamente esa jornada ambos equipos quedan con 0 puntos.</li>
                    <li>**Tarjeta Amarila:** De cada 3 amarillas un partido de sanción para el jugador.</li>
                    <li>Un jugador que lleve 2 o más rojas dependiendo de como fueron las rojas se verá si sigue en el torneo o no. No se ve la necesidad de hacer una falta contra tu compañero que se puede hasta lesionar cuando esto es para divertirse, no se juega por dinero. (No importa si es falta táctica o no. Ojo con las rojas).</li>
                    <li>**Reporte de Datos:** Cada capitán tiene que entregar a los encargados los goles, asistencias y tarjetas que hubieron en el partido lo antes posible y con exactitud. Esto incluye el orden correcto y tiempo aproximado en que se dieron los hechos. (Pueden pedirle a el árbitro o designar a alguien responsable que se encargué de esto).</li>
                </ul>
            </div>

            {{-- 3. SECCIÓN: LOGÍSTICA DE CALENDARIO Y TIEMPOS --}}
            <div class="space-y-4">
                <h3 class="text-2xl font-bold text-red-400 border-b border-gray-700 pb-1">3. Organización y Tiempos de Juego</h3>
                <ul class="list-disc list-inside space-y-3 text-gray-300 ml-4">
                    <li>**Fechas de Partidos:** Hay un calendario simplemente para saber contra quien te va a tocar por jornada. Ahora bien, las fechas de los partidos van a ser pactados entre los equipos.</li>
                    <li>**No Adelantar Jornadas:** No se va a saltar de jornada hasta que cada equipo allá jugado la misma. El ir adelantado partidos no va a existir. Cuando todos los equipos jueguen la jornada correspondiente entonces se pasa para la siguiente, sin importar la demora de un partido.</li>
                    <li>**Límite de 21 Días:** Tampoco podemos esperar un mes por un encuentro. El equipo que se demore más de 21 días aproximadamente 3 semana en cuadrar su partido, automáticamente pierde el equipo que nunca pudo cuadrar o de no ponerse de acuerdo ninguno de los 2 equipos y pasar este tiempo ambos se van con 0 puntos esa jornada. Por lo general la mayor responsabilidad recae sobre el visitante.</li>
                    <li>**Localía y Ayuda Monetaria:** Recordar que es un partido de visitante y otro de local en un mes. En el terreno que decidan jugar los equipos es su responsabilidad, al igual que los acuerdos de ayuda monetaria. No existe ni se exigirá ninguna ley al respecto.</li>
                    <li>**Equipación:** De coincidor los colores de los uniformes de los equipos, el visitante está obligado a cambiar su uniforme.</li>
                </ul>
            </div>
            
            {{-- 4. SECCIÓN: ASPECTOS ECONÓMICOS Y VALORACIÓN --}}
            <div class="space-y-4">
                <h3 class="text-2xl font-bold text-red-400 border-b border-gray-700 pb-1">4. Aspectos Económicos y Valoración Post-Partido</h3>
                
                <ul class="list-disc list-inside space-y-3 text-gray-300 ml-4">
                    <li>**Pago de Árbitros:** Todos los árbitro no cobran lo mismo. No se pongan que si este cobra más o este cobra menos o ¿por qué a este hay que darle tanto? Unos 50 por jugador otro 100. Hay que respetar el pago de los árbitros, no puede haber problemas con eso. Y siempre antes o después del partido entregar el dinero rápido.</li>
                    
                    <li>**Valoración de Capitanes:** Después de cada partido los capitanes van a hacer una valoración de como fue el partido para saber en qué se puede mejorar o si no están conforme con algo y la pondrán en el grupo de los capitanes para ser analizado.</li>
                    
                    <li>**Financiamiento del Sitio Web:** Tenemos un sitio de internet para que todos puedan ver los resultados, el calendario, los goleadores y asistentes entre otros datos interesantes lo antes posible. El servidor deja almacenar cierta cantidad de Datos, si se supera la cantidad exigida por la compañía, es necesario pagar una mensualidad para poder mantenerlo. Esta pagina la creó un hermanito que le gusta hacer este trabajo y dedica de su tiempo para ello pero no puede dedicar también de sus recursos, ya sería abusar de su apoyo. Por tanto, de suceder lo antes expuesto cada equipo tendrá que aportar 2.50 - 5 usd o al cambio para pagar la mensualidad del servidor, este pago se realizará una sola vez durante todo el torneo por equipo. En caso de no aparecer el dinero se quitará la página.</li>
                </ul>
            </div>

            {{-- 5. SECCIÓN: FORMATO DEL TORNEO --}}
            <div class="space-y-4">
                <h3 class="text-2xl font-bold text-red-400 border-b border-gray-700 pb-1">5. Formato y Sistema de Eliminación</h3>
                <ul class="list-disc list-inside space-y-3 text-gray-300 ml-4">
                    <li>**Fase de Grupos:** Se jugará una fase de grupos formato liga de todos contra todos (ida y vuelta).</li>
                    <li>**Clasificación y Repechaje:** Al terminar la jornada 10, el 1er y 2do lugar de la liga se clasificarán automáticamente a la semifinal, los otros 4 equipos jugarán un repechaje por dos puestos restantes.</li>
                    <li>**Orden de Desempate (Tabla):** <ol class="list-decimal list-inside ml-4">
                            <li>Puntos obtenidos.</li>
                            <li>Diferencia de goles a favor y en contra. (La tabla de la página solo mostrará el orden hasta este punto).</li>
                            <li>Más goles a favor.</li>
                            <li>Resultados entre ambos equipos.</li>
                        </ol>
                    </li>
                    <li>**Sorteo:** Ya sabiendo quiénes son los 4 equipos que van a repechaje se realizará un sorteo para definir quién será el rival de cada cual.</li>
                    <li>**Fase Eliminatoria:** El resto de la competición será a partido único. Eliminación directa.</li>
                    <li>**Localía:** Las sede de estos partidos la tendrá el equipo que haya quedado primero en la clasificación. A excepción de las semifinales y la final que serán en Guira, Ceiba de Agua o la Polar dependiendo la que se elija en el momento.</li>
                    <li>**Semifinal:** Los ganadores del repechaje se enfrentarán al primero y segundo lugar en la semifinal. Esta vez el 1ro de la liga recibirá al de más bajo lugar y el 2do al de un puesto superior.</li>
                    <li>**Empates en Eliminación:** En todos los partidos de eliminación directa de haber empate se pasará a una tanda de penales.</li>
                </ul>
            </div>

            <div class="text-sm text-gray-500 pt-4 border-t border-gray-700">
                <p>Organizadores del torneo: José David, Noel y Julio (Sniffy).</p>
            </div>
        </div>
    </div>
@endsection