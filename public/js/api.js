// --- CONFIGURACIÓN Y FUNCIONES BASE ---

// ¡IMPORTANTE! REEMPLAZA ESTO CON LA RUTA REAL A TU ARCHIVO PHP EN EL SERVIDOR
export const API_BASE_URL = window.GLOBAL_API_BASE_URL; 

// Función global para mostrar custom modal (reemplaza alert())
export function showMessage(title, message, isError = false) {
    const modal = document.getElementById('custom-alert');
    const titleElement = document.getElementById('alert-title');
    const messageElement = document.getElementById('alert-message');
    const modalButton = modal.querySelector('button');
    const modalCard = modal.querySelector('.card');

    titleElement.textContent = title;
    messageElement.textContent = message;

    modalCard.classList.remove('border-green-500', 'border-red-500');
    titleElement.classList.remove('text-green-400', 'text-red-400');
    modalButton.classList.remove('bg-green-600', 'bg-red-600', 'hover:bg-green-700', 'hover:bg-red-700');
    
    if (isError) {
        titleElement.classList.add('text-red-400');
        modalCard.classList.add('border-red-500');
        modalButton.classList.add('bg-red-600', 'hover:bg-red-700');
    } else {
        titleElement.classList.add('text-green-400');
        modalCard.classList.add('border-green-500');
        modalButton.classList.add('bg-green-600', 'hover:bg-green-700');
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Función general para peticiones GET a la API
export async function fetchAPI(resource, options = {}) { // ⬅️ ACEPTAR OPTIONS
    try {
        const response = await fetch(`${API_BASE_URL}${resource}`, {
            method: options.method || 'GET',
            headers: options.method === 'POST' ? { 'Content-Type': 'application/json' } : {}, // Solo si es POST
            body: options.body || null,
        });
        
        // El resto del código de manejo de respuesta (response.ok, response.json()...) sigue igual.
        if (!response.ok) {
            // Intenta leer el JSON de error si está disponible
            const errorData = await response.json().catch(() => ({ message: `Respuesta de red no satisfactoria (${response.status})` }));
            throw new Error(errorData.message || `Error al obtener ${resource}`);
        }
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || `Error al obtener ${resource}`);
        }
        return data.data || data.message; // Devuelve data.data o data.message si data.data es nulo
    } catch (error) {
        console.error(`Fallo en fetchAPI para ${resource}:`, error);
        throw error;
    }
}
