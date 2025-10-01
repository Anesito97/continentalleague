// players.js

import { showMessage, API_BASE_URL } from './api.js'; 
import { getIsAdminLoggedIn } from './auth.js';
// ⬇️ Necesitamos estas funciones de main.js y crud.js
import { loadTeamsAndPlayers, players } from './main.js'; 
import { addEventRow } from './crud.js'; 

/**
 * Guarda un nuevo jugador, maneja la subida opcional de fotos.
 * Mueve la función savePlayer de crud.js a players.js.
 */
export async function savePlayer() {
    if (!getIsAdminLoggedIn()) {
        showMessage('Acceso Denegado', 'Debes iniciar sesión como administrador para registrar jugadores.', true);
        return;
    }
    
    const form = document.getElementById('player-form');
    const saveBtn = document.getElementById('save-player-btn');
    
    if (document.getElementById('player-team').value === "") {
        showMessage('Faltan Datos', 'Debes seleccionar un equipo.', true);
        return;
    }
    
    const formData = new FormData(form);
    // Aseguramos que las claves para PHP son 'name', 'number', 'teamId', 'position'
    formData.set('name', document.getElementById('player-name').value);
    formData.set('number', document.getElementById('player-number').value);
    formData.set('teamId', document.getElementById('player-team').value);
    formData.set('position', document.getElementById('player-position').value);
    
    const photoFile = document.getElementById('player-photo').files[0];
    if (photoFile) {
         formData.append('photo', photoFile); // Clave 'photo'
    }

    saveBtn.disabled = true;
    saveBtn.textContent = 'Guardando...';
    
    try {
        const response = await fetch(`${API_BASE_URL}players`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (response.status === 201 && data.success) {
            showMessage('Éxito', data.message + (data.photo_url ? ` (Foto guardada)` : ''), false);
            form.reset();
            loadTeamsAndPlayers();
        } else {
            throw new Error(data.message || `Error al guardar jugador (Status: ${response.status})`);
        }
    } catch (error) {
        console.error("Error al guardar jugador:", error);
        showMessage('Error de Guardado', `Ocurrió un error: ${error.message}. Verifica el endpoint 'players' de la API y la subida de archivos.`, true);
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Guardar Jugador (Vía API)';
    }
}

/**
 * Mueve la función addEventRow de crud.js a players.js.
 * Nota: Esta función necesita acceso al caché de 'players' de main.js.
 */
export function addEventRow() {
    const container = document.getElementById('events-container');
    const newRow = document.createElement('div');
    newRow.className = 'flex flex-col sm:flex-row gap-2 card p-3 border border-gray-600 transition duration-150';
    
    let playerOptions = players.map(p => 
        `<option value="${p.id}">${p.nombre} (#${p.numero}) - ${p.equipo_nombre}</option>`
    ).join('');
    
    newRow.innerHTML = `
        <select name="event_type[]" class="w-full sm:w-1/3 px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm">
            <option value="Gol">Gol</option>
            <option value="Asistencia">Asistencia</option>
            <option value="Parada">Parada (Portero)</option>
            <option value="Amarilla">Tarjeta Amarilla</option>  
            <option value="Roja">Tarjeta Roja</option>
        </select>
        <select name="player_id[]" class="w-full sm:flex-grow px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm">
            <option value="">Seleccionar Jugador...</option>
            ${playerOptions}
        </select>
        <input type="number" name="minuto[]" placeholder="Minuto" class="w-16 px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm">
        <button type="button" onclick="this.parentNode.remove()" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-md text-sm font-semibold flex-shrink-0">X</button>
    `;
    container.appendChild(newRow);
}


// Exportar funciones al scope global para el HTML
window.savePlayer = savePlayer;
window.addEventRow = addEventRow;