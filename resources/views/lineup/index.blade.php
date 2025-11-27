@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-900 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <h1
                    class="text-3xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-500 drop-shadow-lg">
                    Pizarra Táctica
                </h1>
                <p class="text-gray-400 mt-2">Crea y comparte tu alineación ideal</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Controls -->
                <div class="w-full lg:w-1/4 space-y-6">
                    <!-- Team Selector -->
                    <div class="bg-gray-800 p-4 rounded-xl border border-gray-700">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Seleccionar Equipo</label>
                        <select id="teamSelect"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="">-- Elige un equipo --</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Formation Selector -->
                    <div class="bg-gray-800 p-4 rounded-xl border border-gray-700">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Formación</label>
                        <select id="formationSelect"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="4-4-2">4-4-2</option>
                            <option value="4-3-3">4-3-3</option>
                            <option value="3-4-3">3-4-3</option>
                            <option value="3-5-2">3-5-2</option>
                            <option value="5-3-2">5-3-2</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="bg-gray-800 p-4 rounded-xl border border-gray-700 space-y-3">
                        <button id="resetBtn"
                            class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-colors">
                            Reiniciar
                        </button>
                        <button id="downloadBtn"
                            class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            Descargar Imagen
                        </button>
                    </div>
                </div>

                <!-- Pitch Area -->
                <div class="w-full lg:w-3/4 flex justify-center">
                    <div id="pitch-container"
                        class="relative w-full max-w-[600px] aspect-[2/3] bg-green-800 rounded-lg border-4 border-white shadow-2xl overflow-hidden select-none">
                        <!-- Grass Pattern -->
                        <div class="absolute inset-0 opacity-20"
                            style="background-image: repeating-linear-gradient(0deg, transparent, transparent 20px, #000 20px, #000 40px);">
                        </div>

                        <!-- Pitch Markings -->
                        <div class="absolute inset-4 border-2 border-white/50"></div> <!-- Touchline -->
                        <div class="absolute top-1/2 left-4 right-4 h-0.5 bg-white/50 -translate-y-1/2"></div>
                        <!-- Halfway line -->
                        <div
                            class="absolute top-1/2 left-1/2 w-24 h-24 border-2 border-white/50 rounded-full -translate-x-1/2 -translate-y-1/2">
                        </div> <!-- Center circle -->

                        <!-- Penalty Areas -->
                        <div
                            class="absolute top-4 left-1/2 w-48 h-24 border-2 border-t-0 border-white/50 -translate-x-1/2 bg-white/5">
                        </div>
                        <div
                            class="absolute bottom-4 left-1/2 w-48 h-24 border-2 border-b-0 border-white/50 -translate-x-1/2 bg-white/5">
                        </div>

                        <!-- Goal Areas -->
                        <div class="absolute top-4 left-1/2 w-20 h-8 border-2 border-t-0 border-white/50 -translate-x-1/2">
                        </div>
                        <div
                            class="absolute bottom-4 left-1/2 w-20 h-8 border-2 border-b-0 border-white/50 -translate-x-1/2">
                        </div>

                        <!-- Branding Watermark -->
                        <div class="absolute bottom-2 right-4 text-white/30 font-bold text-sm z-0">
                            Continental League
                        </div>

                        <!-- Player Slots Container -->
                        <div id="formation-layer" class="absolute inset-0 z-10 p-4">
                            <!-- Slots will be injected here by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Player Selection Modal -->
    <div id="playerModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" id="modalBackdrop">
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-white mb-4" id="modal-title">Seleccionar Jugador</h3>

                    <!-- Search Input -->
                    <div class="mb-4">
                        <input type="text" id="playerSearch" placeholder="Buscar jugador..."
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-green-500 focus:border-green-500 px-4 py-2">
                    </div>

                    <div id="playersList" class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto">
                        <!-- Players injected here -->
                    </div>
                </div>
                <div class="bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="closeModalBtn"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-800 text-base font-medium text-gray-300 hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const teamSelect = document.getElementById('teamSelect');
            const formationSelect = document.getElementById('formationSelect');
            const formationLayer = document.getElementById('formation-layer');
            const playerModal = document.getElementById('playerModal');
            const playersList = document.getElementById('playersList');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const modalBackdrop = document.getElementById('modalBackdrop');
            const downloadBtn = document.getElementById('downloadBtn');
            const resetBtn = document.getElementById('resetBtn');
            const playerSearch = document.getElementById('playerSearch');

            let currentTeamPlayers = [];
            let activeSlotIndex = null;
            let lineupState = {}; // Key: slotIndex, Value: playerObject

            // Formations Configuration (Percentages for Top/Left)
            // Assuming a vertical pitch. Top 0% is GK (top goal), Bottom 100% is opponent? 
            // Usually lineups are shown bottom-up (GK at bottom) or top-down. 
            // Let's do GK at BOTTOM for standard TV view perspective.
            const formations = {
                '4-4-2': [
                    { top: '85%', left: '50%' }, // GK
                    { top: '65%', left: '20%' }, { top: '65%', left: '40%' }, { top: '65%', left: '60%' }, { top: '65%', left: '80%' }, // DEF
                    { top: '40%', left: '20%' }, { top: '40%', left: '40%' }, { top: '40%', left: '60%' }, { top: '40%', left: '80%' }, // MID
                    { top: '15%', left: '35%' }, { top: '15%', left: '65%' } // FWD
                ],
                '4-3-3': [
                    { top: '85%', left: '50%' }, // GK
                    { top: '65%', left: '20%' }, { top: '65%', left: '40%' }, { top: '65%', left: '60%' }, { top: '65%', left: '80%' }, // DEF
                    { top: '40%', left: '30%' }, { top: '40%', left: '50%' }, { top: '40%', left: '70%' }, // MID
                    { top: '15%', left: '20%' }, { top: '15%', left: '50%' }, { top: '15%', left: '80%' } // FWD
                ],
                '3-4-3': [
                    { top: '85%', left: '50%' }, // GK
                    { top: '65%', left: '30%' }, { top: '65%', left: '50%' }, { top: '65%', left: '70%' }, // DEF
                    { top: '40%', left: '20%' }, { top: '40%', left: '40%' }, { top: '40%', left: '60%' }, { top: '40%', left: '80%' }, // MID
                    { top: '15%', left: '20%' }, { top: '15%', left: '50%' }, { top: '15%', left: '80%' } // FWD
                ],
                '3-5-2': [
                    { top: '85%', left: '50%' }, // GK
                    { top: '65%', left: '30%' }, { top: '65%', left: '50%' }, { top: '65%', left: '70%' }, // DEF
                    { top: '40%', left: '15%' }, { top: '40%', left: '35%' }, { top: '40%', left: '50%' }, { top: '40%', left: '65%' }, { top: '40%', left: '85%' }, // MID
                    { top: '15%', left: '35%' }, { top: '15%', left: '65%' } // FWD
                ],
                '5-3-2': [
                    { top: '85%', left: '50%' }, // GK
                    { top: '65%', left: '15%' }, { top: '65%', left: '32%' }, { top: '65%', left: '50%' }, { top: '65%', left: '68%' }, { top: '65%', left: '85%' }, // DEF
                    { top: '40%', left: '30%' }, { top: '40%', left: '50%' }, { top: '40%', left: '70%' }, // MID
                    { top: '15%', left: '35%' }, { top: '15%', left: '65%' } // FWD
                ]
            };

            // Initialize
            renderFormation('4-4-2');

            // Event Listeners
            teamSelect.addEventListener('change', async (e) => {
                const teamId = e.target.value;
                lineupState = {}; // Reset lineup
                renderFormation(formationSelect.value); // Re-render empty slots

                if (teamId) {
                    try {
                        const response = await fetch(`/lineup-builder/players/${teamId}`);
                        currentTeamPlayers = await response.json();
                    } catch (error) {
                        console.error('Error fetching players:', error);
                        currentTeamPlayers = [];
                    }
                } else {
                    currentTeamPlayers = [];
                }
            });

            formationSelect.addEventListener('change', (e) => {
                renderFormation(e.target.value);
            });

            resetBtn.addEventListener('click', () => {
                lineupState = {};
                renderFormation(formationSelect.value);
            });

            downloadBtn.addEventListener('click', async () => {
                const pitch = document.getElementById('pitch-container');

                // Show loading state
                const originalText = downloadBtn.innerHTML;
                downloadBtn.innerHTML = 'Generando...';
                downloadBtn.disabled = true;

                try {
                    // Pre-process images to Base64 to avoid CORS issues
                    const images = pitch.querySelectorAll('img');
                    const originalSrcs = [];

                    for (let img of images) {
                        originalSrcs.push(img.src);
                        try {
                            const response = await fetch(img.src);
                            const blob = await response.blob();
                            const reader = new FileReader();
                            await new Promise((resolve) => {
                                reader.onloadend = () => {
                                    img.src = reader.result;
                                    resolve();
                                };
                                reader.readAsDataURL(blob);
                            });
                        } catch (e) {
                            console.warn('Could not convert image to base64:', img.src, e);
                        }
                    }

                    const canvas = await html2canvas(pitch, {
                        useCORS: true,
                        scale: 2, // High res
                        backgroundColor: '#1f2937', // Ensure dark background
                        logging: false
                    });

                    // Restore original images
                    images.forEach((img, i) => {
                        img.src = originalSrcs[i];
                    });

                    const link = document.createElement('a');
                    link.download = 'mi-alineacion-continental.png';
                    link.href = canvas.toDataURL('image/png');
                    link.click();

                } catch (error) {
                    console.error('Error generating image:', error);
                    alert('Hubo un error al generar la imagen. Por favor intenta de nuevo.');
                } finally {
                    downloadBtn.innerHTML = originalText;
                    downloadBtn.disabled = false;
                }
            });

            // Modal Controls
            const closeModal = () => playerModal.classList.add('hidden');
            closeModalBtn.addEventListener('click', closeModal);
            modalBackdrop.addEventListener('click', closeModal);

            playerSearch.addEventListener('input', (e) => {
                renderPlayerList(e.target.value);
            });

            function getProxyUrl(url) {
                if (!url) return '';
                return `/lineup-builder/proxy?url=${encodeURIComponent(url)}`;
            }

            function renderFormation(formationName) {
                formationLayer.innerHTML = '';
                const positions = formations[formationName];

                positions.forEach((pos, index) => {
                    const slot = document.createElement('div');
                    slot.className = 'absolute transform -translate-x-1/2 -translate-y-1/2 flex flex-col items-center cursor-pointer transition hover:scale-110';
                    slot.style.top = pos.top;
                    slot.style.left = pos.left;
                    slot.dataset.index = index;

                    const player = lineupState[index];

                    if (player) {
                        const imageUrl = player.foto_url ? getProxyUrl(player.foto_url) : null;

                        // Filled Slot
                        // Added w-32 to container, mx-auto to children, removed backdrop-blur
                        slot.innerHTML = `
                                <div class="w-32 flex flex-col items-center justify-center text-center">
                                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full border-2 border-white overflow-hidden bg-gray-800 shadow-lg relative mx-auto">
                                        ${imageUrl
                                ? `<img src="${imageUrl}" class="w-full h-full object-cover" crossorigin="anonymous">`
                                : `<div class="w-full h-full flex items-center justify-center font-bold text-white text-xl">${player.nombre.charAt(0)}</div>`
                            }
                                        <div class="absolute bottom-0 right-0 bg-yellow-500 text-black text-[10px] font-bold px-1 rounded-tl leading-none">
                                            ${player.numero ?? '-'}
                                        </div>
                                    </div>
                                    <div class="mt-1 bg-black/70 px-2 py-0.5 rounded-full mx-auto inline-block">
                                        <span class="text-white text-[10px] md:text-xs font-bold whitespace-nowrap block">${player.nombre}</span>
                                    </div>
                                </div>
                            `;
                    } else {
                        // Empty Slot
                        slot.innerHTML = `
                                <div class="w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-dashed border-white/50 flex items-center justify-center bg-white/10 hover:bg-white/20 transition-colors mx-auto">
                                    <span class="text-white font-bold text-xl">+</span>
                                </div>
                            `;
                    }

                    slot.addEventListener('click', () => openPlayerSelection(index));
                    formationLayer.appendChild(slot);
                });
            }

            function openPlayerSelection(index) {
                if (!teamSelect.value) {
                    alert('Por favor selecciona un equipo primero.');
                    return;
                }
                activeSlotIndex = index;
                playerSearch.value = ''; // Reset search
                renderPlayerList();
                playerModal.classList.remove('hidden');
                playerSearch.focus();
            }

            function renderPlayerList(searchTerm = '') {
                playersList.innerHTML = '';

                // Filter out already selected players
                const selectedPlayerIds = Object.values(lineupState).map(p => p.id);

                let availablePlayers = currentTeamPlayers.filter(p => !selectedPlayerIds.includes(p.id));

                // Filter by search term
                if (searchTerm) {
                    const term = searchTerm.toLowerCase();
                    availablePlayers = availablePlayers.filter(p =>
                        p.nombre.toLowerCase().includes(term) ||
                        (p.numero && p.numero.toString().includes(term))
                    );
                }

                if (availablePlayers.length === 0) {
                    playersList.innerHTML = '<p class="text-gray-400 text-center py-4">No se encontraron jugadores.</p>';
                    return;
                }

                availablePlayers.forEach(player => {
                    const btn = document.createElement('button');
                    btn.className = 'w-full text-left px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg flex items-center gap-3 transition-colors';
                    // Use direct URL for list (no CORS needed for simple display usually, but consistent is better)
                    // Actually list doesn't need crossOrigin, so direct URL is fine and faster.
                    btn.innerHTML = `
                                <div class="w-8 h-8 rounded-full bg-gray-500 overflow-hidden flex-shrink-0">
                                    ${player.foto_url
                            ? `<img src="${player.foto_url}" class="w-full h-full object-cover">`
                            : `<div class="w-full h-full flex items-center justify-center text-white font-bold">${player.nombre.charAt(0)}</div>`
                        }
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm">${player.nombre}</div>
                                    <div class="text-gray-400 text-xs">${player.posicion_general} #${player.numero ?? '?'}</div>
                                </div>
                            `;
                    btn.addEventListener('click', () => selectPlayer(player));
                    playersList.appendChild(btn);
                });
            }

            function selectPlayer(player) {
                lineupState[activeSlotIndex] = player;
                renderFormation(formationSelect.value);
                closeModal();
            }
        });
    </script>
@endsection