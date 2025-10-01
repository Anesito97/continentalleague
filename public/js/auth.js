import { showMessage, fetchAPI } from './api.js';
import { showView, loadTeamsAndPlayers } from './main.js';

// Usamos sessionStorage para persistir el estado de login a través de la navegación
// OJO: Esto no es seguro, pero es el método más simple sin usar tokens JWT.
let isAdminLoggedIn = !!sessionStorage.getItem('admin_user'); 

export function getIsAdminLoggedIn() {
    return isAdminLoggedIn;
}

export async function handleLogin(username, password) {
    // --- LLAMADA FETCH REAL PARA LOGIN ---
    try {
        const userData = await fetchAPI('login', {
            method: 'POST',
            body: JSON.stringify({ username, password })
        });

        // Si la llamada es exitosa, se obtiene userData
        const userId = userData.user_id;
        const adminUsername = userData.username;

        // Persistir estado de sesión (simulación simple)
        sessionStorage.setItem('admin_user', adminUsername);
        isAdminLoggedIn = true;
        
        // Actualizar UI
        document.getElementById('auth-btn').textContent = 'Logout (Admin)';
        document.getElementById('auth-btn').classList.replace('bg-green-600', 'bg-red-600');
        document.getElementById('admin-user-info').textContent = `Admin: ${adminUsername}`;
        
        showMessage('Login Exitoso', 'Acceso concedido al panel de administración.', false);
        showView('admin');

    } catch (error) {
        console.error("Error de Login:", error);
        showMessage('Fallo de Login', `Error: ${error.message}.`, true);
    }
}

export function toggleAdminView() {
    if (!isAdminLoggedIn) {
        // Si no está logueado, pide credenciales
        const username = prompt("Usuario Admin:");
        const password = prompt("Contraseña:");
        if (username && password) {
            handleLogin(username, password);
        } else if (username !== null || password !== null) {
            showMessage('Login Cancelado', 'Debes ingresar credenciales para acceder al panel admin.', true);
        }
    } else {
        // ⬇️ CORRECCIÓN DE NAVEGACIÓN
        const currentView = document.querySelector('.view-panel:not(.hidden)').id.replace('-view', '');
        
        if (currentView === 'admin') {
            // Si está en la vista admin y presiona, es para HACER LOGOUT
            isAdminLoggedIn = false;
            sessionStorage.removeItem('admin_user'); // Limpiar sesión
            document.getElementById('auth-btn').textContent = 'Admin Login';
            document.getElementById('auth-btn').classList.replace('bg-red-600', 'bg-green-600');
            document.getElementById('admin-user-info').textContent = 'ID de Sesión: N/A (Usar Login)';
            showView('home');
        } else {
            // Si está en otra vista y presiona, es para VOLVER a la vista admin
            showView('admin');
        }
    }
}

// Inicializa el botón en el estado correcto al cargar la página
window.onload = () => {
    const adminUser = sessionStorage.getItem('admin_user');
    if (adminUser) {
        isAdminLoggedIn = true;
        document.getElementById('auth-btn').textContent = 'Logout (Admin)';
        document.getElementById('auth-btn').classList.replace('bg-green-600', 'bg-red-600');
        document.getElementById('admin-user-info').textContent = `Admin: ${adminUser}`;
    }
};

// Para usar en el HTML (GLOBAL)
window.toggleAdminView = toggleAdminView;