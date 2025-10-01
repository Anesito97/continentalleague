import { showMessage, fetchAPI, API_BASE_URL } from './api.js'; 
import { getIsAdminLoggedIn } from './auth.js';
import { loadTeamsAndPlayers, players, currentMatchDetails, setCurrentMatchDetails } from './main.js';

// --- FUNCIONES DE GESTIÓN DE DATOS ---

/**
 * Guarda un nuevo equipo en la base de datos MySQL a través de la API.
 */
export async function saveTeam() {
    if (!getIsAdminLoggedIn()) {
        showMessage('Acceso Denegado', 'Debes iniciar sesión como administrador para registrar equipos.', true);
        return;
    }
    
    // Captura de datos del formulario (SOLO NOMBRE Y LOGO)
    const name = document.getElementById('team-name').value.trim();
    const logoFile = document.getElementById('team-logo').files[0]; 
    const saveBtn = document.getElementById('save-team-btn');

    if (!name) {
        showMessage('Faltan Datos', 'El nombre del equipo es obligatorio.', true);
        return;
    }

    saveBtn.disabled = true;
    saveBtn.textContent = 'Guardando...';

    // Usamos FormData para el archivo (logo) y los campos de texto
    const formData = new FormData();
    // ⬇️ CORRECCIÓN CLAVE: Usar 'name' y añadir 'achievements' (aunque esté vacío) 
    // para que PHP no falle al intentar acceder a esas claves en $_POST.
    formData.append('name', name); 
    formData.append('achievements', ''); 
    
    if (logoFile) {
         formData.append('logo', logoFile);
    }

    try {
        const response = await fetch(`${API_BASE_URL}teams`, {
            method: 'POST',
            body: formData // Usamos FormData para el archivo
        });
        const data = await response.json();
        
        if (response.status === 201 && data.success) {
            showMessage('Éxito', data.message, false);
            document.getElementById('team-form').reset();
            loadTeamsAndPlayers(); // Recarga la caché y actualiza las listas y selects
        } else {
            throw new Error(data.message || `Error al guardar equipo (Status: ${response.status})`);
        }
    } catch (error) {
        console.error("Error al guardar equipo:", error);
        showMessage('Error de Guardado', `Ocurrió un error: ${error.message}. Verifica el endpoint 'teams' de la API y la conexión.`, true);
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Guardar Equipo (Vía API)';
    }
}

/**
 * Programa un nuevo partido.
 */
export async function scheduleMatch() {
     if (!getIsAdminLoggedIn()) {
        showMessage('Acceso Denegado', 'Debes iniciar sesión como administrador.', true);
        return;
    }
    const localId = document.getElementById('match-local').value;
    const visitorId = document.getElementById('match-visitor').value;
    const date = document.getElementById('match-date').value;
    const time = document.getElementById('match-time').value;

    if (localId === visitorId) {
        showMessage('Error de Datos', 'El equipo local y el visitante no pueden ser el mismo.', true);
        return;
    }
    
    const payload = {
        localId: localId, // ⬇️ CLAVES ADAPTADAS A api.php
        visitorId: visitorId,
        dateTime: date + ' ' + time // Formato combinado para DATETIME
    };
    
    try {
        const response = await fetch(`${API_BASE_URL}matches`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        
        if (response.status === 201 && data.success) {
            showMessage('Éxito', data.message, false);
            document.getElementById('matches-content').querySelector('form').reset();
            loadTeamsAndPlayers();
        } else {
            throw new Error(data.message || `Error al programar partido (Status: ${response.status})`);
        }
    } catch (error) {
        console.error("Error al programar partido:", error);
        showMessage('Error de Programación', `Ocurrió un error: ${error.message}. Verifica el endpoint 'matches' de la API.`, true);
    }
}

/**
 * Maneja la finalización de un partido y el envío de eventos (goles, asistencias, paradas).
 */
export async function handleFinalizeMatch() {
     if (!getIsAdminLoggedIn()) {
        showMessage('Acceso Denegado', 'Debes iniciar sesión como administrador para registrar jugadores.', true);
        return;
    }
    if (!currentMatchDetails) {
        showMessage('Error', 'Debes seleccionar un partido para finalizar.', true);
        return;
    }

    // Recoger los datos del formulario
    const golesLocal = parseInt(document.getElementById('goles-local').value);
    const golesVisitor = parseInt(document.getElementById('goles-visitor').value);
    const eventRows = document.querySelectorAll('#events-container > div');
    
    // Mapear los eventos dinámicos
    const matchEvents = Array.from(eventRows).map(row => ({
        event_type: row.querySelector('select[name="event_type[]"]').value,
        player_id: row.querySelector('select[name="player_id[]"]').value,
        minuto: row.querySelector('input[name="minuto[]"]').value
    }));

    const payload = {
        match_id: currentMatchDetails.id,
        goles_local: golesLocal,
        goles_visitor: golesVisitor,
        events: matchEvents
    };

    // Lógica REAL de la API (DEBES IMPLEMENTAR ESTO EN api.php)
    try {
        const response = await fetch(`${API_BASE_URL}finalize-match`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        
        if (response.status === 200 && data.success) {
            showMessage('Partido Finalizado', data.message, false);
            document.getElementById('finalize-match-form').classList.add('hidden');
            document.getElementById('finalize-match-info').classList.remove('hidden');
            loadTeamsAndPlayers(); // Recarga y recalcula la clasificación
        } else {
            throw new Error(data.message || `Error al finalizar partido (Status: ${response.status})`);
        }
    } catch (error) {
        console.error("Error al finalizar partido:", error);
        showMessage('Error de Finalización', `Ocurrió un error: ${error.message}. ¡Revisa la implementación del endpoint 'finalize-match' en api.php!`, true);
    }
}


// Hacer las funciones disponibles globalmente para el HTML
// Esto es necesario porque el HTML utiliza onclick="..."
window.saveTeam = saveTeam;
window.scheduleMatch = scheduleMatch;
window.handleFinalizeMatch = handleFinalizeMatch;