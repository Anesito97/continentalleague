@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-900 text-white flex flex-col items-center py-10">
        <h1 class="text-4xl font-bold mb-4 text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-red-500">
            Keepy Uppy Challenge
        </h1>
        <p class="mb-8 text-gray-400">¡Mantén el balón en el aire! Haz clic o toca el balón para dominarlo.</p>

        <div class="flex flex-col md:flex-row gap-8 w-full max-w-6xl px-4">

            <!-- GAME AREA -->
            <!-- GAME AREA -->
            <div class="flex-1 flex flex-col items-center">
                <div id="game-container"
                    class="relative w-full max-w-md h-[600px] rounded-xl border-4 border-gray-700 overflow-hidden shadow-2xl cursor-pointer select-none bg-cover bg-center"
                    style="background-image: url('https://images.unsplash.com/photo-1522778119026-d647f0565c6a?q=80&w=2070&auto=format&fit=crop');">

                    <!-- Overlay for better visibility -->
                    <div class="absolute inset-0 bg-black/30"></div>

                    <!-- Score Display -->
                    <div class="absolute top-4 left-0 w-full text-center pointer-events-none z-10">
                        <span id="current-score"
                            class="text-7xl font-black text-white drop-shadow-[0_4px_4px_rgba(0,0,0,0.8)] font-display">0</span>
                    </div>

                    <!-- Start Message -->
                    <div id="start-message"
                        class="absolute inset-0 flex items-center justify-center bg-black/60 z-20 backdrop-blur-sm">
                        <div class="text-center animate-pulse">
                            <span class="material-symbols-outlined text-6xl text-white mb-4">touch_app</span>
                            <p class="text-3xl font-bold text-white font-display">TOCA PARA EMPEZAR</p>
                        </div>
                    </div>

                    <!-- Game Over Screen -->
                    <div id="game-over-screen"
                        class="hidden absolute inset-0 flex flex-col items-center justify-center bg-black/80 z-20 backdrop-blur-md">
                        <h2 class="text-5xl font-black text-red-500 mb-2 font-display drop-shadow-lg">GAME OVER</h2>
                        <p class="text-2xl mb-6 text-white">Puntuación: <span id="final-score"
                                class="font-bold text-yellow-400">0</span></p>

                        <div class="flex flex-col gap-4">
                            <button id="restart-btn"
                                class="px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-400 hover:to-pink-500 rounded-full font-bold text-white text-xl transition transform hover:scale-105 shadow-lg hover:shadow-purple-500/50 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">replay</span>
                                Jugar de Nuevo
                            </button>
                        </div>
                    </div>

                    <!-- The Ball -->
                    <div id="ball"
                        class="absolute w-16 h-16 bg-white rounded-full shadow-[0_10px_20px_rgba(0,0,0,0.5)] flex items-center justify-center text-black font-bold hidden"
                        style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/d/d3/Soccerball.svg'); background-size: cover;">
                    </div>
                </div>
            </div>

            <!-- LEADERBOARDD -->
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
            const ball = document.getElementById('ball');
            const scoreDisplay = document.getElementById('current-score');
            const startMessage = document.getElementById('start-message');
            const gameOverScreen = document.getElementById('game-over-screen');
            const finalScoreDisplay = document.getElementById('final-score');
            const restartBtn = document.getElementById('restart-btn');

            let isPlaying = false;
            let score = 0;
            let ballX = 0;
            let ballY = 0;
            let velocityX = 0;
            let velocityY = 0;
            let rotation = 0;

            // Difficulty Variables
            let currentGravity = 0.6;
            let ballScale = 1;
            let wind = 0;
            let level = 1;

            // Base Physics
            const BASE_GRAVITY = 0.6;
            const BOUNCE = -14;
            const FRICTION = 0.99;
            let FLOOR_Y = container.offsetHeight - 70;

            function updateDimensions() {
                FLOOR_Y = container.offsetHeight - 70;
                // Keep ball within bounds if screen shrinks
                if (ballY > FLOOR_Y) ballY = FLOOR_Y;
                if (ballX > container.offsetWidth - 64) ballX = container.offsetWidth - 64;
            }

            window.addEventListener('resize', updateDimensions);

            ball.style.willChange = 'transform';

            function initGame() {
                score = 0;
                level = 1;
                currentGravity = BASE_GRAVITY;
                ballScale = 1;
                wind = 0;

                scoreDisplay.textContent = '0';
                scoreDisplay.style.color = 'white';

                // Wait for transition or just update immediately
                setTimeout(updateDimensions, 100);

                ballX = container.offsetWidth / 2;
                ballY = container.offsetHeight / 2;
                velocityX = 0;
                velocityY = 0;
                rotation = 0;

                ball.style.transform = `translate(${ballX}px, ${ballY}px) rotate(${rotation}deg) scale(${ballScale})`;

                startMessage.classList.add('hidden');
                gameOverScreen.classList.add('hidden');

                isPlaying = true;
                gameLoop();
            }

            function updateDifficulty() {
                // Level 1: 0-14 (Normal)
                if (score < 15) {
                    level = 1;
                    currentGravity = BASE_GRAVITY;
                    ballScale = 1;
                    ball.style.opacity = '1';
                    scoreDisplay.style.color = 'white';
                }
                // Level 2: 15-29 (Heavy Ball)
                else if (score >= 15 && score < 30) {
                    if (level < 2) showToast("¡Gravedad Aumentada!");
                    level = 2;
                    currentGravity = BASE_GRAVITY * 1.3; // 30% heavier
                    scoreDisplay.style.color = '#facc15'; // Yellow
                }
                // Level 3: 30-49 (Small Ball)
                else if (score >= 30 && score < 50) {
                    if (level < 3) showToast("¡Balón Pequeño!");
                    level = 3;
                    ballScale = 0.7; // 70% size
                    scoreDisplay.style.color = '#fb923c'; // Orange
                }
                // Level 4: 50-74 (Windy)
                else if (score >= 50 && score < 75) {
                    if (level < 4) showToast("¡Viento Fuerte!");
                    level = 4;
                    wind = (Math.random() - 0.5) * 0.3;
                    scoreDisplay.style.color = '#ef4444'; // Red
                }
                // Level 5: 75+ (Ghost Ball)
                else if (score >= 75) {
                    if (level < 5) showToast("¡Modo Fantasma!");
                    level = 5;
                    ball.style.opacity = '0.4'; // Hard to see
                    wind = (Math.random() - 0.5) * 0.5; // Stronger wind
                    scoreDisplay.style.color = '#a855f7'; // Purple
                }
            }

            function showToast(text) {
                const toast = document.createElement('div');
                // Fix: Allow wrapping, center text, constrain width, adjust font size for mobile
                toast.className = 'absolute top-24 left-1/2 transform -translate-x-1/2 text-xl md:text-3xl font-bold text-white drop-shadow-lg animate-bounce z-40 pointer-events-none text-center w-11/12 bg-black/30 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/20';
                toast.textContent = text;
                container.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            }

            function gameOver() {
                isPlaying = false;
                finalScoreDisplay.textContent = score;
                gameOverScreen.classList.remove('hidden');
                saveScore(score);
            }

            function gameLoop() {
                if (!isPlaying) return;

                // Physics
                velocityY += currentGravity;

                // Wind update for levels 4+
                if (level >= 4) {
                    // Fluctuating wind
                    wind += (Math.random() - 0.5) * 0.1;
                    // Clamp wind
                    wind = Math.max(-0.5, Math.min(0.5, wind));
                    velocityX += wind;
                }

                ballX += velocityX;
                ballY += velocityY;
                velocityX *= FRICTION;

                // Walls
                if (ballX <= 0) {
                    ballX = 0;
                    velocityX *= -0.8;
                } else if (ballX >= container.offsetWidth - (64 * ballScale)) {
                    ballX = container.offsetWidth - (64 * ballScale);
                    velocityX *= -0.8;
                }

                // Floor
                if (ballY >= FLOOR_Y) {
                    ballY = FLOOR_Y;
                    gameOver();
                    return;
                }

                // Rotation
                rotation += velocityX * 2;

                // Render
                ball.style.transform = `translate3d(${ballX}px, ${ballY}px, 0) rotate(${rotation}deg) scale(${ballScale})`;

                requestAnimationFrame(gameLoop);
            }

            function handleInput(e) {
                // Fix: Don't prevent default if clicking a button (though stopPropagation on button should handle this)
                if (e.target.tagName === 'BUTTON') return;

                // Prevent default browser behavior (scrolling, zooming)
                if (e.cancelable) e.preventDefault();

                if (!isPlaying && !gameOverScreen.classList.contains('hidden')) return;

                // Get coordinates (Touch or Mouse)
                let clientX, clientY;
                if (e.type === 'touchstart') {
                    clientX = e.touches[0].clientX;
                    clientY = e.touches[0].clientY;
                } else {
                    clientX = e.clientX;
                    clientY = e.clientY;
                }

                if (!isPlaying) {
                    initGame();
                    kickBall(clientX);
                } else {
                    // Hit detection
                    const rect = ball.getBoundingClientRect();
                    // Adjust center based on current scale
                    const currentSize = 64 * ballScale;
                    const ballCenterX = rect.left + (currentSize / 2);
                    const ballCenterY = rect.top + (currentSize / 2);

                    // Hit area scales with ball but stays generous
                    const hitRadius = 120 * ballScale;

                    const dist = Math.hypot(clientX - ballCenterX, clientY - ballCenterY);

                    if (dist < hitRadius) {
                        kickBall(clientX);
                    }
                }
            }

            function kickBall(inputX) {
                const rect = ball.getBoundingClientRect();
                const currentSize = 64 * ballScale;
                const ballCenterX = rect.left + (currentSize / 2);

                // Add upward force
                velocityY = BOUNCE;

                // Horizontal force based on hit position
                // -1 (left edge) to 1 (right edge)
                // Clamped to avoid extreme angles
                let offset = (ballCenterX - inputX) / (40 * ballScale);
                offset = Math.max(-1.5, Math.min(1.5, offset));

                velocityX = offset * 6;

                score++;
                scoreDisplay.textContent = score;

                updateDifficulty();

                // Visual pop
                // We need to maintain the current scale while popping
                const popScale = ballScale * 0.9;
                ball.style.transform = `translate3d(${ballX}px, ${ballY}px, 0) rotate(${rotation}deg) scale(${popScale})`;
                setTimeout(() => {
                    ball.style.transform = `translate3d(${ballX}px, ${ballY}px, 0) rotate(${rotation}deg) scale(${ballScale})`;
                }, 50);
            }

            // Listeners
            startMessage.addEventListener('click', () => {
                initGame();
            });

            restartBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                initGame();
            });
            restartBtn.addEventListener('touchstart', (e) => {
                e.stopPropagation();
                e.preventDefault();
                initGame();
            });

            // Prevent scrolling on the game container
            container.addEventListener('touchmove', (e) => {
                if (e.cancelable) e.preventDefault();
            }, { passive: false });

            // Input Handling
            container.addEventListener('click', (e) => {
                if (!isPlaying && !gameOverScreen.classList.contains('hidden')) return;
                if (!isPlaying) {
                    initGame();
                    // First tap shouldn't hit the ball immediately to avoid instant game over if bad spawn
                    return;
                }

                const rect = container.getBoundingClientRect();
                const clientX = e.clientX - rect.left;
                kickBall(clientX);
            });

            container.addEventListener('touchstart', (e) => {
                if (!isPlaying && !gameOverScreen.classList.contains('hidden')) return;
                if (!isPlaying) {
                    e.preventDefault();
                    initGame();
                    return;
                }

                const rect = container.getBoundingClientRect();
                const clientX = e.touches[0].clientX - rect.left;
                kickBall(clientX);
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
                        body: JSON.stringify({ score: score })
                    });
                } catch (error) {
                    console.error('Error saving score:', error);
                }
            }
        });
    </script>
@endsection