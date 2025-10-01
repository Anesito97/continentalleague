import { showMessage, fetchAPI } from './api.js';

const DEFAULT_PLAYER_IMAGE = 'https://placehold.co/100x100/1f2937/FFFFFF?text=JUGADOR';

// Función de renderizado para Clasificación
export function renderStandings(teamsData, playersData) {
    const standingsBody = document.getElementById('standings-body');
    standingsBody.innerHTML = '';
    
    if (teamsData.length === 0) {
         standingsBody.innerHTML = '<tr class="hover:bg-gray-700 transition"><td colspan="9" class="py-4 text-center text-gray-500">Aún no hay equipos.</td></tr>';
         return;
    }
    
    // Ordenar por puntos, luego por diferencia de goles
    teamsData.sort((a, b) => {
        const puntosDiff = (b.puntos || 0) - (a.puntos || 0);
        if (puntosDiff !== 0) return puntosDiff;
        const gdA = (a.goles_a_favor || 0) - (a.goles_en_contra || 0);
        const gdB = (b.goles_a_favor || 0) - (b.goles_en_contra || 0);
        return gdB - gdA;
    });

    teamsData.forEach((team, index) => {
         const row = document.createElement('tr');
         row.className = 'hover:bg-gray-700 transition';
         row.innerHTML = `
            <td class="py-3 px-2 font-bold ${index === 0 ? 'text-yellow-400' : 'text-gray-300'}">${index + 1}</td>
            <td class="py-3 px-2 flex items-center">
                <img src="${team.escudo_url || 'https://placehold.co/50x50/1f2937/FFFFFF?text=LOGO'}" 
                     onerror="this.src='https://placehold.co/50x50/1f2937/FFFFFF?text=LOGO'"
                     class="w-8 h-8 rounded-full object-cover mr-3" />
                <span class="font-medium text-white">${team.nombre}</span>
            </td>
            <td class="py-3 px-2 text-center font-bold text-green-400">${team.puntos || 0}</td>
            <td class="py-3 px-2 text-center">${team.partidos_jugados || 0}</td>
            <td class="py-3 px-2 text-center">${team.ganados || 0}</td>
            <td class="py-3 px-2 text-center hidden sm:table-cell">${team.empatados || 0}</td>
            <td class="py-3 px-2 text-center hidden sm:table-cell">${team.perdidos || 0}</td>
            <td class="py-3 px-2 text-center hidden sm:table-cell">${team.goles_a_favor || 0}</td>
            <td class="py-3 px-2 text-center hidden sm:table-cell">${team.goles_en_contra || 0}</td>
         `;
         standingsBody.appendChild(row);
     });
}

// Función de renderizado para Partidos Recientes (Próximos Partidos)
export function loadRecentMatches(pendingMatches) {
    const recentMatchesEl = document.getElementById('recent-matches');
    
    if (pendingMatches.length === 0) {
        recentMatchesEl.innerHTML = '<p class="text-sm text-gray-400">No hay partidos pendientes.</p>';
        return;
    }
    
    recentMatchesEl.innerHTML = pendingMatches.slice(0, 5).map(match => {
        const dateTime = new Date(match.fecha + ' ' + match.hora);
        const formattedDate = dateTime.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric', month: 'short' });
        const formattedTime = dateTime.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

        return `
            <div class="p-2 border-l-4 border-green-500 bg-gray-700 rounded-md flex justify-between items-center text-sm">
                <span>${match.local_name} <span class="font-bold text-green-400">vs</span> ${match.visitor_name}</span>
                <span class="text-xs text-gray-400">${formattedDate}, ${formattedTime}</span>
            </div>
        `;
    }).join('');
}

// Funciones de renderizado para Estadísticas
export function renderTopScorers(playersData) {
    const topScorersEl = document.getElementById('top-scorers');
    playersData.sort((a, b) => (b.goles || 0) - (a.goles || 0));
    topScorersEl.innerHTML = playersData.slice(0, 5).map((p, i) => 
        `<li class="flex justify-between items-center text-gray-300">
            <span class="font-semibold">${i + 1}. ${p.nombre} (${p.equipo_nombre ? p.equipo_nombre.substring(0, 20) : 'N/A'})</span>
            <span class="text-red-400 font-bold">${p.goles || 0}</span>
        </li>`
    ).join('') || '<li class="text-gray-500">Aún no hay goles registrados.</li>';
}

export function renderTopAssists(playersData) {
    const topAssistsEl = document.getElementById('top-assists');
    playersData.sort((a, b) => (b.asistencias || 0) - (a.asistencias || 0));
    topAssistsEl.innerHTML = playersData.slice(0, 5).map((p, i) => 
        `<li class="flex justify-between items-center text-gray-300">
            <span class="font-semibold">${i + 1}. ${p.nombre} (${p.equipo_nombre ? p.equipo_nombre.substring(0, 20) : 'N/A'})</span>
            <span class="text-yellow-400 font-bold">${p.asistencias || 0}</span>
        </li>`
    ).join('') || '<li class="text-gray-500">Aún no hay asistencias registradas.</li>';
}

export function renderTopKeepers(playersData) {
    const topKeepersEl = document.getElementById('top-keepers');
    const keepers = playersData.filter(p => (p.posicion || '').toLowerCase() === 'portero');
    keepers.sort((a, b) => (b.paradas || 0) - (a.paradas || 0));
    
    topKeepersEl.innerHTML = keepers.slice(0, 5).map((p, i) => 
        `<li class="flex justify-between items-center text-gray-300">
            <span class="font-semibold">${i + 1}. ${p.nombre} (${p.equipo_nombre ? p.equipo_nombre.substring(0, 20) : 'N/A'})</span>
            <span class="text-blue-400 font-bold">${p.paradas || 0}</span>
        </li>`
    ).join('') || '<li class="text-gray-500">Aún no hay porteros con paradas registradas.</li>';
}

// Funciones de renderizado para Listas de Admin y Selectores
export function renderAdminLists(teamsData, playersData, pendingMatchesData) {
    // 1. Listado de Equipos Admin
    const teamsListEl = document.getElementById('teams-list');
    teamsListEl.innerHTML = '';
    
    if (teamsData.length === 0) {
         teamsListEl.innerHTML = '<li class="p-2 text-center text-gray-500">No hay equipos registrados aún.</li>';
    } else {
        teamsData.forEach(team => {
            const li = document.createElement('li');
            li.className = 'p-2 bg-gray-700 rounded-md flex justify-between items-center';
            li.innerHTML = `<span>${team.nombre} (Ptos: ${team.puntos || 0})</span>`;
            teamsListEl.appendChild(li);
        });
    }

    // 2. Selectores de Equipo para Jugadores y Partidos
    const teamSelects = document.querySelectorAll('#player-team, #match-local, #match-visitor');
    teamSelects.forEach(select => {
        const currentVal = select.value;
        select.innerHTML = '<option value="">Seleccionar Equipo...</option>';
        teamsData.forEach(team => {
            const option = document.createElement('option');
            option.value = team.id;
            option.textContent = team.nombre;
            select.appendChild(option);
        });
         select.value = currentVal;
    });

    // 3. Listado de Jugadores Admin
    const playersListEl = document.getElementById('players-list');
    playersListEl.innerHTML = '';
    
    if (playersData.length === 0) {
         playersListEl.innerHTML = '<li class="p-2 text-center text-gray-500">No hay jugadores registrados.</li>';
    } else {
        playersData.forEach(player => {
            const li = document.createElement('li');
            li.className = 'p-2 bg-gray-700 rounded-md flex justify-between items-center';
            li.innerHTML = `
                <div class="flex items-center space-x-3">
                    <img src="${player.foto_url || DEFAULT_PLAYER_IMAGE}" onerror="this.src='${DEFAULT_PLAYER_IMAGE}'" class="w-8 h-8 rounded-full object-cover">
                    <span>${player.nombre} (#${player.numero})</span>
                </div>
                <span class="text-xs text-gray-400">${player.equipo_nombre} - ${player.posicion}</span>
            `;
            playersListEl.appendChild(li);
        });
    }
    
    // 4. Selectores y Lista de Partidos Pendientes
    const pendingMatchesListEl = document.getElementById('pending-matches');
    const finalizeSelect = document.getElementById('match-to-finalize');
    pendingMatchesListEl.innerHTML = '';
    finalizeSelect.innerHTML = '<option value="">Selecciona Partido...</option>';

    if (pendingMatchesData.length === 0) {
        pendingMatchesListEl.innerHTML = '<li class="p-2 text-center text-gray-500">No hay partidos pendientes.</li>';
        finalizeSelect.innerHTML = '<option value="">No hay partidos pendientes</option>';
    } else {
        pendingMatchesData.forEach(match => {
            const dateTime = new Date(match.fecha + ' ' + match.hora);
            const formattedDate = dateTime.toLocaleDateString('es-ES');
            
            // Lista de Partidos Pendientes
            const li = document.createElement('li');
            li.className = 'p-2 bg-gray-700 rounded-md flex justify-between items-center';
            li.innerHTML = `
                <span class="font-medium">${match.local_name} vs ${match.visitor_name}</span>
                <span class="text-xs text-gray-400">${formattedDate}</span>
            `;
            pendingMatchesListEl.appendChild(li);
            
            // Selector de Finalizar Partido
            const option = document.createElement('option');
            option.value = match.id;
            option.textContent = `${match.local_name} vs ${match.visitor_name} (${formattedDate})`;
            finalizeSelect.appendChild(option);
        });
    }
}
