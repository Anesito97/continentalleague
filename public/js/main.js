import { fetchAPI } from './api.js';
import { renderStandings, renderTopScorers, renderTopAssists, renderTopKeepers, loadRecentMatches, renderAdminLists } from './render.js';

// --- ESTADO GLOBAL (Cache) ---
export let teams = [];
export let players = [];
export let pendingMatches = [];
export let currentMatchDetails = null;

export function setCurrentMatchDetails(details) {
    currentMatchDetails = details;
}

// --- LÓGICA DE NAVEGACIÓN ---
export function showView(viewId) {
    const mainViews = document.querySelectorAll('.view-panel');
    mainViews.forEach(view => {
        view.classList.add('hidden');
    });
    document.getElementById(viewId + '-view').classList.remove('hidden');
    if (viewId === 'admin') {
        showAdminContent('teams');
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

export function showAdminContent(contentId) {
    const adminContents = document.querySelectorAll('.admin-content');
    const adminTitle = document.getElementById('admin-title');

    adminContents.forEach(content => {
        content.classList.add('hidden');
    });
    const contentElement = document.getElementById(contentId + '-content');
    if (contentElement) {
        contentElement.classList.remove('hidden');
        let titleMap = {
            'teams': 'Gestión de Equipos',
            'players': 'Gestión de Jugadores',
            'matches': 'Gestión de Partidos',
            'finalize-match': 'Finalizar Partido'
        };
        adminTitle.textContent = titleMap[contentId] || 'Panel de Administración';
        
        if(contentId === 'finalize-match') {
            document.getElementById('finalize-match-form').classList.add('hidden');
            document.getElementById('finalize-match-info').classList.remove('hidden');
            loadTeamsAndPlayers();
        }
    }
}

// --- LÓGICA DE CARGA INICIAL Y CACHÉ ---
export async function loadTeamsAndPlayers() {
    // 1. Cargar datos de la API
    teams = await fetchAPI('teams');
    players = await fetchAPI('players');
    pendingMatches = await fetchAPI('matches/pending');
    
    // 2. Renderizar Vistas Públicas
    renderStandings(teams, players);
    renderTopScorers(players);
    renderTopAssists(players);
    renderTopKeepers(players);
    loadRecentMatches(pendingMatches);
    
    // 3. Renderizar Vistas de Admin
    renderAdminLists(teams, players, pendingMatches);
}

// --- LÓGICA DE FINALIZAR PARTIDO ---
export function loadMatchDetails(matchId) {
    const form = document.getElementById('finalize-match-form');
    const infoText = document.getElementById('finalize-match-info');
    
    if (!matchId) {
        form.classList.add('hidden');
        infoText.classList.remove('hidden');
        setCurrentMatchDetails(null);
        return;
    }
    
    const match = pendingMatches.find(m => m.id == matchId);
    setCurrentMatchDetails(match);
    
    if (match) {
        document.getElementById('local-team-name').textContent = match.local_name;
        document.getElementById('visitor-team-name').textContent = match.visitor_name;
        
        document.getElementById('events-container').innerHTML = '';
        window.addEventRow(); // Utilizar la función global del CRUD
        
        form.classList.remove('hidden');
        infoText.classList.add('hidden');
    }
}


// --- INICIALIZACIÓN ---
document.addEventListener('DOMContentLoaded', () => {
    showView('home');
    loadTeamsAndPlayers();
});

// Poner funciones esenciales en el scope global para el HTML
window.showView = showView;
window.showAdminContent = showAdminContent;
window.loadMatchDetails = loadMatchDetails;
