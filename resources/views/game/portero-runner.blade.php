@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-900 text-white flex flex-col items-center py-10">
        <h1 class="text-4xl font-bold mb-4 text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-500">
            Portero Runner
        </h1>
        <p class="mb-8 text-gray-400">¡Muévete entre los carriles para atrapar los balones y evitar las bombas!</p>

        <div class="flex flex-col md:flex-row gap-8 w-full max-w-6xl px-4">

            <!-- GAME AREA -->
            <div class="flex-1 flex flex-col items-center">
                <div id="game-container"
                    class="relative w-full max-w-md h-[600px] rounded-xl border-4 border-gray-700 overflow-hidden shadow-2xl select-none bg-gray-800">

                    <!-- Background Lanes -->
                    <div class="absolute inset-0 flex w-full h-full">
                        <div class="flex-1 border-r border-gray-700/30 bg-gray-800/50"></div>
                        <div class="flex-1 border-r border-gray-700/30 bg-gray-700/20"></div>
                        <div class="flex-1 bg-gray-800/50"></div>
                    </div>

                    <!-- Fog Overlay -->
                    <div id="fog-overlay"
                        class="absolute inset-0 bg-gradient-to-b from-gray-900 via-transparent to-transparent opacity-0 transition-opacity duration-1000 pointer-events-none z-0">
                    </div>

                    <!-- Score Display -->
                    <div class="absolute top-4 left-0 w-full text-center pointer-events-none z-10">
                        <span id="current-score"
                            class="text-7xl font-black text-white drop-shadow-[0_4px_4px_rgba(0,0,0,0.8)] font-display">0</span>
                    </div>

                    <!-- Lives Display -->
                    <div id="lives-display" class="absolute top-4 right-4 flex gap-1 z-10">
                        <span
                            class="material-symbols-outlined text-red-500 text-3xl drop-shadow-md animate-pulse">favorite</span>
                        <span
                            class="material-symbols-outlined text-red-500 text-3xl drop-shadow-md animate-pulse">favorite</span>
                        <span
                            class="material-symbols-outlined text-red-500 text-3xl drop-shadow-md animate-pulse">favorite</span>
                    </div>

                    <!-- Start Message -->
                    <div id="start-message"
                        class="absolute inset-0 flex items-center justify-center bg-black/60 z-20 backdrop-blur-sm cursor-pointer">
                        <div class="text-center animate-pulse">
                            <span class="material-symbols-outlined text-6xl text-white mb-4">touch_app</span>
                            <p class="text-3xl font-bold text-white font-display">TOCA PARA EMPEZAR</p>
                        </div>
                    </div>

                    <!-- Game Over Screen -->
                    <div id="game-over-screen"
                        class="hidden absolute inset-0 flex flex-col items-center justify-center bg-black/80 z-30 backdrop-blur-md">
                        <h2 class="text-5xl font-black text-red-500 mb-2 font-display drop-shadow-lg">GAME OVER</h2>
                        <p class="text-2xl mb-6 text-white">Puntuación: <span id="final-score"
                                class="font-bold text-yellow-400">0</span></p>
                        <button id="restart-btn"
                            class="px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 rounded-full font-bold text-white text-xl transition transform hover:scale-105 shadow-lg hover:shadow-green-500/50 flex items-center gap-2">
                            <span class="material-symbols-outlined">replay</span>
                            Jugar de Nuevo
                        </button>
                    </div>

                    <!-- Player (Goal) -->
                    <div id="player"
                        class="absolute bottom-4 w-24 h-16 border-4 border-white rounded-t-lg transition-transform duration-100 ease-linear z-10 flex items-end justify-center">
                        <div class="w-full h-full bg-white/10 backdrop-blur-sm"></div>
                        <!-- Net pattern -->
                        <div class="absolute inset-0 opacity-30"
                            style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 4px 4px;">
                        </div>
                    </div>

                    <!-- Items Container -->
                    <div id="items-container" class="absolute inset-0 overflow-hidden pointer-events-none">
                        <!-- Items will be injected here -->
                    </div>

                </div>

                <!-- Controls Hint -->
                <div class="mt-4 text-gray-400 text-sm hidden md:block">
                    Usa las flechas <span class="font-bold text-white">←</span> y <span
                        class="font-bold text-white">→</span> para moverte
                </div>
            </div>

            <!-- LEADERBOARD -->
            <div class="w-full md:w-80 bg-gray-800 rounded-xl p-6 border border-gray-700 h-fit">
                <h2 class="text-2xl font-bold mb-4 text-yellow-400 border-b border-gray-700 pb-2">Top 10 Jugadores</h2>

                <div class="mb-6">
                    <p class="text-sm text-gray-400">Tu Mejor Puntuación:</p>
                    <p class="text-3xl font-bold text-green-400">{{ $userBest }}</p>
                </div>

                <ul class="space-y-3">
                    @foreach($topScores as $index => $score)
                        <li
                            class="flex items-center justify-between p-2 rounded bg-gray-700/50 {{ $score->user_id == Auth::id() ? 'border border-yellow-500/50' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-gray-500 w-6">#{{ $index + 1 }}</span>
                                <div class="flex items-center gap-2">
                                    @if($score->user->avatar)
                                        <img src="{{ $score->user->avatar }}" class="w-6 h-6 rounded-full">
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-gray-600 flex items-center justify-center text-xs">
                                            {{ substr($score->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="text-sm font-medium truncate max-w-[120px]">{{ $score->user->name }}</span>
                                </div>
                            </div>
                            <span class="font-bold text-yellow-400">{{ $score->score }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('game-container');
            const player = document.getElementById('player');
            const itemsContainer = document.getElementById('items-container');
            const scoreDisplay = document.getElementById('current-score');
            const livesDisplay = document.getElementById('lives-display');
            const startMessage = document.getElementById('start-message');
            const gameOverScreen = document.getElementById('game-over-screen');
            const finalScoreDisplay = document.getElementById('final-score');
            const restartBtn = document.getElementById('restart-btn');

            // Game State
            let isPlaying = false;
            let score = 0;
            let lives = 3;
            let currentLane = 1; // 0: Left, 1: Center, 2: Right
            let gameLoopId;
            let spawnLoopId;
            let difficultyLoopId;
            let items = [];

            // Powerup States
            let hasShield = false;
            let magnetActive = false;
            let isFrozen = false;

            // Settings
            const LANES_COUNT = 3;
            let gameSpeed = 5;
            let spawnRate = 1000;
            let scoreMultiplier = 1;
            let playerWidth = 1; // 1 lane width

            // Constants
            const LANE_WIDTH_PERCENT = 100 / LANES_COUNT;

            function initGame() {
                score = 0;
                lives = 3;
                currentLane = 1;
                gameSpeed = 5;
                spawnRate = 1000;
                scoreMultiplier = 1;
                playerWidth = 1;
                items = [];

                // Reset States
                hasShield = false;
                magnetActive = false;
                isFrozen = false;
                player.classList.remove('border-blue-400', 'shadow-[0_0_15px_rgba(59,130,246,0.8)]', 'opacity-50');
                const fogOverlay = document.getElementById('fog-overlay');
                if (fogOverlay) {
                    fogOverlay.classList.remove('opacity-90');
                    fogOverlay.classList.add('opacity-0');
                }

                // Clear items
                itemsContainer.innerHTML = '';

                scoreDisplay.textContent = '0';
                updateLivesDisplay();
                updatePlayerPosition();

                startMessage.classList.add('hidden');
                gameOverScreen.classList.add('hidden');

                isPlaying = true;

                // Loops
                requestAnimationFrame(gameLoop);
                spawnLoop();
                difficultyLoop();
            }

            function updateLivesDisplay() {
                livesDisplay.innerHTML = '';
                for (let i = 0; i < 3; i++) {
                    const heart = document.createElement('span');
                    heart.className = `material-symbols-outlined text-3xl drop-shadow-md transition-all duration-300 ${i < lives ? 'text-red-500 animate-pulse' : 'text-gray-600'}`;
                    heart.textContent = 'favorite';
                    livesDisplay.appendChild(heart);
                }
            }

            function updatePlayerPosition() {
                // Calculate position based on lane and width
                // Center the player in the lane(s)
                // If width is 1, center in currentLane
                // If width is 2, occupy currentLane and next one (clamp to bounds)
                // If width is 3, occupy all

                let effectiveLane = currentLane;

                // Clamp lane based on width
                if (playerWidth === 2) {
                    if (effectiveLane === 2) effectiveLane = 1; // Can't be in rightmost lane if width 2
                } else if (playerWidth === 3) {
                    effectiveLane = 1; // Always center if full width
                }

                const leftPercent = effectiveLane * LANE_WIDTH_PERCENT;
                const widthPercent = playerWidth * LANE_WIDTH_PERCENT;

                player.style.left = `${leftPercent}%`;
                player.style.width = `${widthPercent}%`;

                // Visual feedback for powerups
                if (playerWidth > 1) {
                    player.classList.add('border-yellow-400');
                    player.classList.remove('border-white');
                } else {
                    player.classList.add('border-white');
                    player.classList.remove('border-yellow-400');
                }
            }

            function moveLane(direction) {
                if (!isPlaying || isFrozen) return;

                const newLane = currentLane + direction;

                // Check bounds
                if (newLane >= 0 && newLane < LANES_COUNT) {
                    currentLane = newLane;
                    updatePlayerPosition();
                }
            }

            function spawnItem() {
                if (!isPlaying) return;

                const typeRoll = Math.random();
                let type = 'ball';

                // 10% Bomb, 5% Yellow Card, 10% Powerup, 75% Ball
                if (typeRoll < 0.1) type = 'bomb';
                else if (typeRoll < 0.15) type = 'yellow_card';
                else if (typeRoll < 0.25) type = 'powerup';

                const lane = Math.floor(Math.random() * LANES_COUNT);

                const item = document.createElement('div');
                item.className = 'absolute w-12 h-12 rounded-full shadow-lg flex items-center justify-center text-xs font-bold transition-transform';
                item.style.left = `${(lane * LANE_WIDTH_PERCENT) + (LANE_WIDTH_PERCENT / 2)}%`;
                item.style.top = '-50px';
                item.style.transform = 'translateX(-50%)';

                // Item Styles
                if (type === 'ball') {
                    item.style.backgroundImage = "url('https://upload.wikimedia.org/wikipedia/commons/d/d3/Soccerball.svg')";
                    item.style.backgroundSize = 'cover';
                    item.style.backgroundColor = 'white';
                } else if (type === 'bomb') {
                    item.textContent = '💣';
                    item.style.backgroundColor = '#ef4444';
                    item.style.fontSize = '24px';
                } else if (type === 'yellow_card') {
                    item.style.backgroundColor = '#facc15'; // Yellow
                    item.style.borderRadius = '4px'; // Card shape
                    item.style.width = '32px';
                    item.style.height = '48px';
                } else {
                    // Random Powerup
                    const pType = Math.random();
                    if (pType < 0.2) { // Life
                        item.dataset.powerup = 'life';
                        item.textContent = '❤️';
                        item.style.backgroundColor = '#ef4444';
                    } else if (pType < 0.4) { // Magnet
                        item.dataset.powerup = 'magnet';
                        item.textContent = '🧲';
                        item.style.backgroundColor = '#3b82f6'; // Blue
                    } else if (pType < 0.6) { // Shield
                        item.dataset.powerup = 'shield';
                        item.textContent = '🛡️';
                        item.style.backgroundColor = '#0ea5e9'; // Sky
                    } else if (pType < 0.8) { // Slow
                        item.dataset.powerup = 'slow';
                        item.textContent = '⏱️';
                        item.style.backgroundColor = '#a855f7';
                    } else { // Double
                        item.dataset.powerup = 'double';
                        item.textContent = '2x';
                        item.style.backgroundColor = '#eab308';
                    }
                }

                itemsContainer.appendChild(item);

                items.push({
                    element: item,
                    y: -50,
                    lane: lane,
                    type: type,
                    powerupType: item.dataset.powerup
                });

                // Schedule next spawn
                setTimeout(spawnItem, spawnRate);
            }

            function spawnLoop() {
                if (!isPlaying) return;
                setTimeout(spawnItem, spawnRate);
            }

            function difficultyLoop() {
                if (!isPlaying) return;

                // Increase difficulty every 5 seconds
                setTimeout(() => {
                    if (!isPlaying) return;
                    gameSpeed += 0.5;
                    spawnRate = Math.max(300, spawnRate - 50);

                    // Fog Logic
                    if (score > 100) {
                        const fogOverlay = document.getElementById('fog-overlay');
                        if (fogOverlay) {
                            fogOverlay.classList.remove('opacity-0');
                            fogOverlay.classList.add('opacity-90');
                        }
                    }

                    difficultyLoop();
                }, 5000);
            }

            function applyPowerup(type) {

                if (type === 'life') {
                    if (lives < 3) {
                        lives++;
                        updateLivesDisplay();
                        showToast("¡VIDA EXTRA!");
                    } else {
                        score += 50; // Bonus points if full health
                        scoreDisplay.textContent = score;
                        showToast("¡BONUS +50!");
                    }
                } else if (type === 'magnet') {
                    showToast("¡IMÁN!");
                    magnetActive = true;
                    setTimeout(() => {
                        magnetActive = false;
                    }, 5000);
                } else if (type === 'shield') {
                    showToast("¡ESCUDO!");
                    hasShield = true;
                    player.classList.add('border-blue-400', 'shadow-[0_0_15px_rgba(59,130,246,0.8)]');
                    player.classList.remove('border-white');
                } else if (type === 'slow') {
                    showToast("¡TIEMPO LENTO!");
                    const originalSpeed = gameSpeed;
                    gameSpeed = gameSpeed * 0.5;
                    setTimeout(() => {
                        gameSpeed = originalSpeed;
                    }, 5000);
                } else if (type === 'double') {
                    showToast("¡PUNTOS DOBLES!");
                    scoreMultiplier = 2;
                    setTimeout(() => {
                        scoreMultiplier = 1;
                    }, 5000);
                }
            }

            function showToast(text) {
                const toast = document.createElement('div');
                toast.className = 'absolute top-24 left-1/2 transform -translate-x-1/2 text-2xl font-bold text-white drop-shadow-lg animate-bounce z-40 pointer-events-none text-center bg-black/50 px-4 py-2 rounded-xl';
                toast.textContent = text;
                container.appendChild(toast);
                setTimeout(() => toast.remove(), 1500);
            }

            function gameLoop() {
                if (!isPlaying) return;

                // Move items
                for (let i = items.length - 1; i >= 0; i--) {
                    const item = items[i];

                    // Magnet Effect
                    if (magnetActive && item.type === 'ball') {
                        // Move towards player lane
                        const targetLaneX = (currentLane * LANE_WIDTH_PERCENT) + (LANE_WIDTH_PERCENT / 2);
                        const currentItemXPercent = parseFloat(item.element.style.left);

                        // Simple lerp
                        const diff = targetLaneX - currentItemXPercent;
                        item.element.style.left = `${currentItemXPercent + (diff * 0.1)}%`;
                    }

                    item.y += gameSpeed;
                    item.element.style.top = `${item.y}px`;

                    // Collision Detection
                    const playerRect = player.getBoundingClientRect();
                    const itemRect = item.element.getBoundingClientRect();
                    const containerRect = container.getBoundingClientRect();

                    // Check if item is within vertical range of player
                    // Simple AABB collision
                    if (
                        itemRect.bottom >= playerRect.top &&
                        itemRect.top <= playerRect.bottom &&
                        itemRect.right >= playerRect.left &&
                        itemRect.left <= playerRect.right
                    ) {
                        // Collision!
                        if (item.type === 'bomb') {
                            if (hasShield) {
                                hasShield = false;
                                player.classList.remove('border-blue-400', 'shadow-[0_0_15px_rgba(59,130,246,0.8)]');
                                player.classList.add('border-white');
                                showToast("¡ESCUDO ROTO!");
                                // Visual pop for shield break
                            } else {
                                lives--;
                                updateLivesDisplay();

                                // Visual feedback
                                container.classList.add('animate-shake');
                                setTimeout(() => container.classList.remove('animate-shake'), 500);

                                if (lives <= 0) {
                                    gameOver();
                                    return;
                                } else {
                                    showToast("¡CUIDADO!");
                                }
                            }
                            item.element.remove();
                            items.splice(i, 1);
                        } else if (item.type === 'yellow_card') {
                            showToast("¡FALTA! (Congelado)");
                            isFrozen = true;
                            player.classList.add('opacity-50');
                            setTimeout(() => {
                                isFrozen = false;
                                player.classList.remove('opacity-50');
                            }, 1500);
                            item.element.remove();
                            items.splice(i, 1);
                        } else if (item.type === 'ball') {
                            score += 1 * scoreMultiplier;
                            scoreDisplay.textContent = score;
                            // Visual pop
                            item.element.remove();
                            items.splice(i, 1);
                        } else if (item.type === 'powerup') {
                            applyPowerup(item.powerupType);
                            item.element.remove();
                            items.splice(i, 1);
                        }
                    }
                    // Out of bounds
                    else if (item.y > container.offsetHeight) {
                        item.element.remove();
                        items.splice(i, 1);
                    }
                }

                requestAnimationFrame(gameLoop);
            }

            function gameOver() {
                isPlaying = false;
                gameOverScreen.classList.remove('hidden');
                finalScoreDisplay.textContent = score;
                saveScore(score);
            }

            // Input Handling
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') moveLane(-1);
                if (e.key === 'ArrowRight') moveLane(1);
            });

            // Touch Handling (Swipe)
            let touchStartX = 0;
            container.addEventListener('touchstart', (e) => {
                touchStartX = e.touches[0].clientX;
                if (!isPlaying && !gameOverScreen.classList.contains('hidden')) return;
                if (!isPlaying) initGame();
            }, { passive: false });

            container.addEventListener('touchend', (e) => {
                const touchEndX = e.changedTouches[0].clientX;
                const diff = touchEndX - touchStartX;

                if (Math.abs(diff) > 30) { // Threshold
                    if (diff > 0) moveLane(1);
                    else moveLane(-1);
                } else {
                    // Tap to start
                    if (!isPlaying && startMessage.classList.contains('hidden') === false) {
                        initGame();
                    }
                }
            });

            // Mouse click to start
            startMessage.addEventListener('click', () => {
                if (!isPlaying) initGame();
            });

            restartBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                initGame();
            });

            async function saveScore(score) {
                if (score === 0) return;
                try {
                    await fetch('{{ route("game.save") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            score: score,
                            game_type: 'portero_runner'
                        })
                    });
                } catch (error) {
                    console.error('Error saving score:', error);
                }
            }
        });
    </script>

    <style>
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .animate-shake {
            animation: shake 0.2s ease-in-out 2;
        }
    </style>
@endsection