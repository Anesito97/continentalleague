@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-900 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-500 drop-shadow-lg">
                    Pizarra Táctica
                </h1>
                <p class="text-gray-400 mt-2">Crea y comparte tu alineación ideal</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <div class="w-full lg:w-1/4 space-y-6">
                    <div class="bg-gray-800 p-4 rounded-xl border border-gray-700">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Seleccionar Equipo</label>
                        <select id="teamSelect" class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="">-- Elige un equipo --</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-gray-800 p-4 rounded-xl border border-gray-700">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Formación</label>
                        <select id="formationSelect" class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="4-4-2">4-4-2</option>
                            <option value="4-3-3">4-3-3</option>
                            <option value="4-2-3-1">4-2-3-1</option>
                            <option value="4-1-4-1">4-1-4-1</option>
                            <option value="4-5-1">4-5-1</option>
                            <option value="3-4-3">3-4-3</option>
                            <option value="3-5-2">3-5-2</option>
                            <option value="5-3-2">5-3-2</option>
                            <option value="5-4-1">5-4-1</option>
                        </select>
                    </div>

                    <div class="bg-gray-800 p-4 rounded-xl border border-gray-700 space-y-3">
                        <button id="resetBtn" class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-colors">
                            Reiniciar
                        </button>
                        <button id="downloadBtn" class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Descargar Imagen
                        </button>
                    </div>
                </div>

                <div class="w-full lg:w-3/4 flex justify-center">
                    <div id="pitch-container" class="relative w-full max-w-[600px] aspect-[2/3] bg-green-800 rounded-lg border-4 border-white shadow-2xl overflow-hidden select-none">
                        <div class="absolute inset-0 opacity-20" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 20px, #000 20px, #000 40px);"></div>

                        <div class="absolute inset-4 border-2 border-white/50"></div> 
                        <div class="absolute top-1/2 left-4 right-4 h-0.5 bg-white/50 -translate-y-1/2"></div>
                        <div class="absolute top-1/2 left-1/2 w-24 h-24 border-2 border-white/50 rounded-full -translate-x-1/2 -translate-y-1/2"></div> 
                        <div class="absolute top-4 left-1/2 w-48 h-24 border-2 border-t-0 border-white/50 -translate-x-1/2 bg-white/5"></div>
                        <div class="absolute bottom-4 left-1/2 w-48 h-24 border-2 border-b-0 border-white/50 -translate-x-1/2 bg-white/5"></div>
                        <div class="absolute top-4 left-1/2 w-20 h-8 border-2 border-t-0 border-white/50 -translate-x-1/2"></div>
                        <div class="absolute bottom-4 left-1/2 w-20 h-8 border-2 border-b-0 border-white/50 -translate-x-1/2"></div>

                        <div class="absolute bottom-2 right-4 text-white/30 font-bold text-sm z-0">
                            Continental League
                        </div>

                        <div id="formation-layer" class="absolute inset-0 z-10 p-4">
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="playerModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" id="modalBackdrop"></div>
        
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                <div class="relative transform overflow-hidden rounded-lg bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full max-h-[85vh] flex flex-col border border-gray-700">
                    
                    <div class="bg-gray-800 px-4 pt-5 pb-2 sm:p-6 sm:pb-4 flex-shrink-0">
                        <h3 class="text-lg leading-6 font-medium text-white mb-4" id="modal-title">Seleccionar Jugador</h3>
                        <div>
                            <input type="text" id="playerSearch" placeholder="Buscar jugador..." autocomplete="off"
                                class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-green-500 focus:border-green-500 px-4 py-2 shadow-inner">
                        </div>
                    </div>

                    <div class="bg-gray-800 px-4 flex-1 overflow-y-auto min-h-[200px]" id="modal-content-scroll">
                        <div id="playersList" class="grid grid-cols-1 gap-2 pb-4">
                            </div>
                    </div>

                    <div class="bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse flex-shrink-0">
                        <button type="button" id="closeModalBtn"
                            class="w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-800 text-base font-medium text-gray-300 hover:bg-gray-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
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
            let lineupState = {};

            // Formations Configuration
            const formations = {
                '4-4-2': [
                    { top: '88%', left: '50%' }, // GK
                    { top: '68%', left: '15%' }, { top: '72%', left: '38%' }, { top: '72%', left: '62%' }, { top: '68%', left: '85%' }, // DEF
                    { top: '45%', left: '15%' }, { top: '50%', left: '38%' }, { top: '50%', left: '62%' }, { top: '45%', left: '85%' }, // MID
                    { top: '18%', left: '35%' }, { top: '18%', left: '65%' } // FWD
                ],
                '4-3-3': [
                    { top: '88%', left: '50%' }, // GK
                    { top: '68%', left: '15%' }, { top: '72%', left: '38%' }, { top: '72%', left: '62%' }, { top: '68%', left: '85%' }, // DEF
                    { top: '48%', left: '30%' }, { top: '52%', left: '50%' }, { top: '48%', left: '70%' }, // MID
                    { top: '20%', left: '20%' }, { top: '15%', left: '50%' }, { top: '20%', left: '80%' } // FWD
                ],
                '4-2-3-1': [
                    { top: '88%', left: '50%' }, // GK
                    { top: '68%', left: '15%' }, { top: '72%', left: '38%' }, { top: '72%', left: '62%' }, { top: '68%', left: '85%' }, // DEF
                    { top: '55%', left: '35%' }, { top: '55%', left: '65%' }, // CDM
                    { top: '35%', left: '20%' }, { top: '35%', left: '50%' }, { top: '35%', left: '80%' }, // CAM/Wingers
                    { top: '15%', left: '50%' } // ST
                ],
                '4-1-4-1': [
                    { top: '88%', left: '50%' }, // GK
                    { top: '68%', left: '15%' }, { top: '72%', left: '38%' }, { top: '72%', left: '62%' }, { top: '68%', left: '85%' }, // DEF
                    { top: '55%', left: '50%' }, // CDM
                    { top: '38%', left: '15%' }, { top: '42%', left: '35%' }, { top: '42%', left: '65%' }, { top: '38%', left: '85%' }, // MID
                    { top: '15%', left: '50%' } // ST
                ],
                '4-5-1': [
                    { top: '88%', left: '50%' }, // GK
                    { top: '68%', left: '15%' }, { top: '72%', left: '38%' }, { top: '72%', left: '62%' }, { top: '68%', left: '85%' }, // DEF
                    { top: '45%', left: '10%' }, { top: '50%', left: '30%' }, { top: '52%', left: '50%' }, { top: '50%', left: '70%' }, { top: '45%', left: '90%' }, // MID
                    { top: '15%', left: '50%' } // ST
                ],
                '3-4-3': [
                    { top: '88%', left: '50%' }, // GK
                    { top: '70%', left: '25%' }, { top: '72%', left: '50%' }, { top: '70%', left: '75%' }, // DEF
                    { top: '45%', left: '15%' }, { top: '50%', left: '38%' }, { top: '50%', left: '62%' }, { top: '45%', left: '85%' }, // MID
                    { top: '20%', left: '20%' }, { top: '15%', left: '50%' }, { top: '20%', left: '80%' } // FWD
                ],
                '3-5-2': [
                    { top: '88%', left: '50%' }, // GK
                    { top: '70%', left: '25%' }, { top: '72%', left: '50%' }, { top: '70%', left: '75%' }, // DEF
                    { top: '45%', left: '15%' }, { top: '50%', left: '35%' }, { top: '52%', left: '50%' }, { top: '50%', left: '65%' }, { top: '45%', left: '85%' }, // MID
                    { top: '18%', left: '35%' }, { top: '18%', left: '65%' } // FWD
                ],
                '5-3-2': [
                    { top: '88%', left: '50%' }, // GK
                    { top: '60%', left: '10%' }, { top: '70%', left: '30%' }, { top: '72%', left: '50%' }, { top: '70%', left: '70%' }, { top: '60%', left: '90%' }, // DEF
                    { top: '45%', left: '30%' }, { top: '48%', left: '50%' }, { top: '45%', left: '70%' }, // MID
                    { top: '18%', left: '35%' }, { top: '18%', left: '65%' } // FWD
                ],
                '5-4-1': [
                    { top: '88%', left: '50%' }, // GK
                    { top: '60%', left: '10%' }, { top: '70%', left: '30%' }, { top: '72%', left: '50%' }, { top: '70%', left: '70%' }, { top: '60%', left: '90%' }, // DEF
                    { top: '45%', left: '25%' }, { top: '48%', left: '42%' }, { top: '48%', left: '58%' }, { top: '45%', left: '75%' }, // MID
                    { top: '15%', left: '50%' } // ST
                ]
            };

            // Initialize
            renderFormation('4-4-2');

            // Event Listeners
            teamSelect.addEventListener('change', async (e) => {
                const teamId = e.target.value;
                lineupState = {};
                renderFormation(formationSelect.value);

                if (teamId) {
                    try {
                        const response = await fetch(`/lineup-builder/players/${teamId}`);
                        if (!response.ok) throw new Error('Error de red');
                        currentTeamPlayers = await response.json();
                    } catch (error) {
                        console.error('Error:', error);
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
                const originalText = downloadBtn.innerHTML;
                downloadBtn.innerHTML = 'Generando...';
                downloadBtn.disabled = true;

                const scrollPos = window.scrollY;
                window.scrollTo(0, 0);

                try {
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
                            console.warn('Proxy fallback:', e);
                        }
                    }

                    await new Promise(r => setTimeout(r, 100));

                    const canvas = await html2canvas(pitch, {
                        useCORS: true,
                        scale: 3,
                        backgroundColor: '#1f2937',
                        scrollY: -window.scrollY,
                        windowWidth: document.documentElement.offsetWidth,
                        windowHeight: document.documentElement.offsetHeight,
                        logging: false,
                        onclone: (clonedDoc) => {
                            const clonedPitch = clonedDoc.getElementById('pitch-container');
                            clonedPitch.style.fontFamily = 'sans-serif';
                        }
                    });

                    images.forEach((img, i) => {
                        img.src = originalSrcs[i];
                    });

                    const link = document.createElement('a');
                    link.download = `alineacion-${teamSelect.options[teamSelect.selectedIndex]?.text.trim() || 'equipo'}.png`;
                    link.href = canvas.toDataURL('image/png', 1.0);
                    link.click();

                } catch (error) {
                    console.error('Error generando imagen:', error);
                    alert('Error al generar la imagen.');
                } finally {
                    downloadBtn.innerHTML = originalText;
                    downloadBtn.disabled = false;
                    window.scrollTo(0, scrollPos);
                }
            });

            // CORRECCIÓN MODAL: Quitamos 'hidden' de la clase base y usamos JS para controlarlo
            // para evitar conflictos con estilos inline
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
                    slot.className = 'absolute transform -translate-x-1/2 -translate-y-1/2 cursor-pointer z-20 hover:scale-105 transition-transform duration-200';
                    slot.style.top = pos.top;
                    slot.style.left = pos.left;
                    slot.dataset.index = index;

                    const player = lineupState[index];

                    if (player) {
                        const imageUrl = player.foto_url ? getProxyUrl(player.foto_url) : null;

                        slot.innerHTML = `
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 md:w-16 md:h-16 rounded-full border-[2px] md:border-[3px] border-white overflow-hidden bg-gray-800 shadow-xl z-10 block">
                                    ${imageUrl
                                        ? `<img src="${imageUrl}" class="w-full h-full object-cover" crossorigin="anonymous">`
                                        : `<div class="w-full h-full flex items-center justify-center font-bold text-white text-lg md:text-xl">${player.nombre.charAt(0)}</div>`
                                    }
                                </div>

                                <div class="h-1"></div>

                                <span class="text-white text-[10px] md:text-[12px] font-bold block leading-none tracking-wide text-center uppercase whitespace-nowrap drop-shadow-[0_2px_3px_rgba(0,0,0,0.9)]">
                                    ${player.nombre}
                                </span>
                            </div>
                        `;
                    } else {
                        slot.innerHTML = `
                            <div class="w-12 h-12 md:w-16 md:h-16 rounded-full border-2 border-dashed border-white/50 flex items-center justify-center bg-white/10 hover:bg-white/20 transition-colors shadow-sm">
                                <span class="text-white font-bold text-xl opacity-80">+</span>
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
                playerSearch.value = '';
                renderPlayerList();
                playerModal.classList.remove('hidden');
                
                // Timeout para asegurar que el input esté visible antes de enfocar
                setTimeout(() => {
                    playerSearch.focus();
                }, 100);
            }

            function renderPlayerList(searchTerm = '') {
                playersList.innerHTML = '';
                const selectedPlayerIds = Object.values(lineupState).map(p => p.id);
                let availablePlayers = currentTeamPlayers.filter(p => !selectedPlayerIds.includes(p.id));

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
                    btn.className = 'w-full text-left px-4 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg flex items-center gap-3 transition-colors border-b border-gray-600 last:border-0';
                    btn.innerHTML = `
                        <div class="w-10 h-10 rounded-full bg-gray-500 overflow-hidden flex-shrink-0 border border-gray-400">
                            ${player.foto_url
                                ? `<img src="${player.foto_url}" class="w-full h-full object-cover">`
                                : `<div class="w-full h-full flex items-center justify-center text-white font-bold">${player.nombre.charAt(0)}</div>`
                            }
                        </div>
                        <div>
                            <div class="text-white font-bold text-sm">${player.nombre}</div>
                            <div class="text-gray-400 text-xs font-mono text-green-400">#${player.numero ?? '?'} - ${player.posicion_general ?? 'Jugador'}</div>
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