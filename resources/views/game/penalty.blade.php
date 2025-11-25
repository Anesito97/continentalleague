@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-900 text-white flex flex-col items-center py-10">
        <div class="flex items-center justify-between w-full max-w-6xl px-4 mb-4">
            <h1
                class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 font-display">
                Penalty Shootout
            </h1>
            <a href="{{ route('games.index') }}"
                class="text-gray-400 hover:text-white flex items-center gap-2 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
                Volver
            </a>
        </div>

        <div class="flex flex-col md:flex-row gap-8 w-full max-w-6xl px-4">

            <!-- GAME AREA -->
            <div class="flex-1 flex flex-col items-center">
                <div id="game-container"
                    class="relative w-full max-w-3xl h-[500px] rounded-xl border-4 border-gray-700 overflow-hidden shadow-2xl cursor-pointer select-none bg-cover bg-center"
                    style="background-image: url('{{ asset('images/games/penalty-bg.png') }}');">

                    <!-- Overlay (Slightly darker for UI contrast) -->
                    <div class="absolute inset-0 bg-black/10"></div>

                    <!-- Score & Streak -->
                    <div class="absolute top-4 left-4 z-10 bg-black/50 px-4 py-2 rounded-lg backdrop-blur-sm">
                        <p class="text-sm text-gray-300">Puntuación</p>
                        <p id="current-score" class="text-3xl font-bold text-white font-display">0</p>
                    </div>
                    <div class="absolute top-4 right-4 z-10 bg-black/50 px-4 py-2 rounded-lg backdrop-blur-sm">
                        <p class="text-sm text-gray-300">Racha</p>
                        <p id="current-streak" class="text-3xl font-bold text-yellow-400 font-display">0</p>
                    </div>

                    <!-- Goal Area (Invisible Hitboxes - Adjusted for new image) -->
                    <!-- Assuming the goal in the image is roughly centered and takes up a good portion -->
                    <div class="absolute top-[25%] left-[10%] w-[80%] h-[50%]"></div>

                    <!-- Goalkeeper -->
                    <div id="goalkeeper"
                        class="absolute top-[35%] left-1/2 transform -translate-x-1/2 w-24 h-32 bg-contain bg-no-repeat bg-center transition-all duration-300"
                        style="background-image: url('https://cdn-icons-png.flaticon.com/512/166/166363.png'); filter: drop-shadow(0 0 10px rgba(0,0,0,0.5));">
                    </div>

                    <!-- Ball -->
                    <div id="ball"
                        class="absolute bottom-[10%] left-1/2 transform -translate-x-1/2 w-16 h-16 bg-white rounded-full shadow-lg transition-all duration-500 ease-out"
                        style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/d/d3/Soccerball.svg'); background-size: cover;">
                    </div>

                    <!-- UI Controls (Aim & Power) -->
                    <div id="controls" class="absolute bottom-4 w-full px-8 flex flex-col gap-2 z-20">

                        <!-- Aim Bar -->
                        <div class="w-full h-4 bg-gray-700 rounded-full overflow-hidden relative border border-gray-500">
                            <div id="aim-cursor"
                                class="absolute top-0 bottom-0 w-2 bg-yellow-400 shadow-[0_0_10px_rgba(250,204,21,0.8)]">
                            </div>
                            <div class="absolute top-0 bottom-0 left-1/2 w-1 h-full bg-white/50"></div>
                            <!-- Center marker -->
                        </div>
                        <p class="text-center text-xs text-gray-300 font-bold">DIRECCIÓN</p>

                        <!-- Power Bar -->
                        <div
                            class="w-full h-4 bg-gray-700 rounded-full overflow-hidden relative border border-gray-500 mt-2">
                            <div id="power-fill" style="will-change: width, background-color;"
                                class="h-full bg-gradient-to-r from-green-500 via-yellow-500 to-red-500 w-0"></div>
                            <div class="absolute top-0 bottom-0 left-[80%] w-1 h-full bg-white/50"></div>
                            <!-- Sweet spot marker -->
                        </div>
                        <p class="text-center text-xs text-gray-300 font-bold">POTENCIA</p>

                    </div>

                    <!-- Messages -->
                    <div id="message-overlay"
                        class="hidden absolute inset-0 flex items-center justify-center z-30 pointer-events-none">
                        <h2 id="message-text"
                            class="text-6xl font-black text-white drop-shadow-[0_5px_5px_rgba(0,0,0,1)] transform scale-150 transition-transform duration-300">
                            GOAL!</h2>
                    </div>

                    <!-- Game Over Screen -->
                    <div id="game-over-screen"
                        class="hidden absolute inset-0 flex flex-col items-center justify-center bg-black/80 z-40 backdrop-blur-md">
                        <h2 class="text-5xl font-black text-red-500 mb-2 font-display">GAME OVER</h2>
                        <p class="text-2xl mb-6 text-white">Puntuación Final: <span id="final-score"
                                class="font-bold text-yellow-400">0</span></p>
                        <button id="restart-btn"
                            class="px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-400 hover:to-purple-500 rounded-full font-bold text-white text-xl transition transform hover:scale-105 shadow-lg flex items-center gap-2">
                            <span class="material-symbols-outlined">replay</span>
                            Intentar de Nuevo
                        </button>
                    </div>

                    <!-- Start Screen -->
                    <div id="start-screen"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-black/60 z-40 backdrop-blur-sm cursor-pointer">
                        <span class="material-symbols-outlined text-6xl text-white mb-4 animate-bounce">sports_soccer</span>
                        <h2 class="text-3xl font-bold text-white font-display mb-2">TANDA DE PENALTIS</h2>
                        <p class="text-gray-300 text-sm mb-8">Toca para fijar Dirección, luego Toca para fijar Potencia</p>
                        <button
                            class="px-6 py-2 bg-white text-black font-bold rounded-full hover:bg-gray-200 transition">JUGAR</button>
                    </div>

                </div>
            </div>

            <!-- LEADERBOARD -->
            <div class="w-full md:w-80 bg-gray-800 rounded-xl p-6 border border-gray-700 h-fit">
                <h2 class="text-2xl font-bold mb-4 text-blue-400 border-b border-gray-700 pb-2">Top Goleadores</h2>

                <div class="mb-6">
                    <p class="text-sm text-gray-400">Tu Mejor Puntuación:</p>
                    <p class="text-3xl font-bold text-green-400">{{ $userBest }}</p>
                </div>

                <ul class="space-y-3">
                    @foreach($topScores as $index => $score)
                        <li
                            class="flex items-center justify-between p-2 rounded bg-gray-700/50 {{ $score->user_id == Auth::id() ? 'border border-blue-500/50' : '' }}">
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
                            <span class="font-bold text-blue-400">{{ $score->score }}</span>
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
            const goalkeeper = document.getElementById('goalkeeper');
            const aimCursor = document.getElementById('aim-cursor');
            const powerFill = document.getElementById('power-fill');
            const scoreDisplay = document.getElementById('current-score');
            const streakDisplay = document.getElementById('current-streak');
            const messageOverlay = document.getElementById('message-overlay');
            const messageText = document.getElementById('message-text');
            const startScreen = document.getElementById('start-screen');
            const gameOverScreen = document.getElementById('game-over-screen');
            const finalScoreDisplay = document.getElementById('final-score');
            const restartBtn = document.getElementById('restart-btn');

            // Game State
            let state = 'START'; // START, AIMING, POWER, SHOOTING, RESULT, GAMEOVER
            let score = 0;
            let streak = 0;
            let lives = 3;

            // Mechanics
            let aimValue = 50; // 0 (Left) to 100 (Right)
            let aimDirection = 1; // 1 or -1
            let aimSpeed = 1.5;

            let powerValue = 0; // 0 to 100
            let powerDirection = 1;
            let powerSpeed = 2;

            let animationFrame;

            function initGame() {
                score = 0;
                streak = 0;
                lives = 3;
                scoreDisplay.textContent = '0';
                streakDisplay.textContent = '0';

                resetTurn();

                startScreen.classList.add('hidden');
                gameOverScreen.classList.add('hidden');
                state = 'AIMING';
                gameLoop();
            }

            function resetTurn() {
                ball.style.transition = 'none';
                ball.style.transform = 'translate(-50%, 0) scale(1)';
                ball.style.bottom = '10%';
                ball.style.left = '50%';

                goalkeeper.style.transform = 'translate(-50%, 0)';
                goalkeeper.style.left = '50%';

                aimValue = 50;
                powerValue = 0;
                powerDirection = 1; // Ensure it always starts moving up
                powerFill.style.width = '0%';
                powerFill.style.backgroundColor = '#22c55e'; // Reset to green
                powerFill.className = 'h-full'; // Keep base class only

                state = 'AIMING';
            }

            function gameLoop() {
                if (state === 'GAMEOVER') return;

                if (state === 'AIMING') {
                    aimValue += aimSpeed * aimDirection;
                    if (aimValue >= 100 || aimValue <= 0) aimDirection *= -1;
                    aimCursor.style.left = `${aimValue}%`;
                } else if (state === 'POWER') {
                    powerValue += powerSpeed * powerDirection;

                    // Clamp and bounce
                    if (powerValue >= 100) {
                        powerValue = 100;
                        powerDirection = -1;
                    } else if (powerValue <= 0) {
                        powerValue = 0;
                        powerDirection = 1;
                    }

                    // Color change based on power using inline styles for performance
                    if (powerValue > 80) powerFill.style.backgroundColor = '#ef4444'; // red-500
                    else if (powerValue > 60) powerFill.style.backgroundColor = '#eab308'; // yellow-500
                    else powerFill.style.backgroundColor = '#22c55e'; // green-500

                    powerFill.style.width = `${powerValue}%`;
                }

                animationFrame = requestAnimationFrame(gameLoop);
            }

            let lastInputTime = 0;
            const INPUT_COOLDOWN = 250; // Reduced to 250ms for better responsiveness

            function handleInput(e) {
                if (e.target.tagName === 'BUTTON') return;
                e.preventDefault();

                const now = Date.now();
                if (now - lastInputTime < INPUT_COOLDOWN) return;

                if (state === 'START') {
                    initGame();
                    lastInputTime = now;
                } else if (state === 'AIMING') {
                    state = 'POWER';
                    lastInputTime = now;
                } else if (state === 'POWER') {
                    state = 'SHOOTING';
                    shoot();
                    lastInputTime = now;
                }
            }

            function shoot() {
                // 1. Calculate Shot Trajectory
                // Aim: 0 (Left) -> 50 (Center) -> 100 (Right)
                // Target X (CSS %): 15% (Left Post) to 85% (Right Post)
                const targetX = 15 + (aimValue * 0.7);

                // Power determines Height and Speed
                // Ideal power is 70-90. Too low = ground, Too high = over bar.
                let targetY = 20; // Default ground
                let isOverBar = false;

                if (powerValue > 95) {
                    isOverBar = true;
                    targetY = -10; // Fly over
                } else if (powerValue > 60) {
                    targetY = 35; // Top corner area
                } else {
                    targetY = 25; // Mid height
                }

                // 2. Goalkeeper AI
                // Reaction depends on difficulty (streak)
                // Simple AI: Random dive but weighted towards center
                let keeperDive = 50;
                const reactionChance = Math.min(0.3 + (streak * 0.05), 0.9); // Gets better with streak

                if (Math.random() < reactionChance) {
                    // Keeper guesses correctly-ish
                    keeperDive = targetX + ((Math.random() - 0.5) * 20);
                } else {
                    // Random dive
                    keeperDive = Math.random() * 100;
                }

                // Clamp keeper
                keeperDive = Math.max(20, Math.min(80, keeperDive));

                // 3. Animate Ball
                ball.style.transition = 'all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                ball.style.bottom = isOverBar ? '120%' : '35%'; // Visual depth
                ball.style.left = `${targetX}%`;
                ball.style.transform = 'translate(-50%, 0) scale(0.5)'; // Shrink for depth

                // 4. Animate Keeper
                goalkeeper.style.transition = 'all 0.5s ease-out';
                goalkeeper.style.left = `${keeperDive}%`;
                // Simple tilt for dive
                const tilt = keeperDive < 50 ? -45 : 45;
                goalkeeper.style.transform = `translate(-50%, 0) rotate(${tilt}deg)`;

                // 5. Determine Result
                setTimeout(() => {
                    let result = 'MISS';

                    if (isOverBar) {
                        result = 'MISS';
                    } else {
                        // Hitbox check
                        // Ball width approx 5% of screen width
                        // Keeper width approx 10%
                        const dist = Math.abs(targetX - keeperDive);

                        // Post collision (approx 15% and 85%)
                        if (targetX < 17 || targetX > 83) {
                            result = 'POST'; // Hit the post (lucky or unlucky)
                            // Bounce back logic could go here
                        } else if (dist < 10) {
                            result = 'SAVED';
                        } else {
                            result = 'GOAL';
                        }
                    }

                    showResult(result);
                }, 600);
            }

            function showResult(result) {
                messageOverlay.classList.remove('hidden');

                if (result === 'GOAL') {
                    messageText.textContent = 'GOAL!';
                    messageText.className = 'text-6xl font-black text-green-500 drop-shadow-[0_5px_5px_rgba(0,0,0,1)] animate-bounce';
                    score += 10 + (streak * 2);
                    streak++;
                    scoreDisplay.textContent = score;
                    streakDisplay.textContent = streak;

                    // Difficulty increase
                    aimSpeed = 1.5 + (streak * 0.2);
                    powerSpeed = 2 + (streak * 0.2);

                } else {
                    messageText.textContent = result === 'SAVED' ? 'SAVED!' : 'MISS!';
                    messageText.className = 'text-6xl font-black text-red-500 drop-shadow-[0_5px_5px_rgba(0,0,0,1)] animate-pulse';
                    streak = 0;
                    streakDisplay.textContent = streak;
                    lives--;

                    // Reset difficulty
                    aimSpeed = 1.5;
                    powerSpeed = 2;
                }

                setTimeout(() => {
                    messageOverlay.classList.add('hidden');
                    if (lives <= 0) {
                        gameOver();
                    } else {
                        resetTurn();
                        gameLoop();
                    }
                }, 1500);
            }

            function gameOver() {
                state = 'GAMEOVER';
                cancelAnimationFrame(animationFrame);
                gameOverScreen.classList.remove('hidden');
                finalScoreDisplay.textContent = score;
                saveScore(score);
            }

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
                            game_type: 'penalty'
                        })
                    });
                } catch (error) {
                    console.error('Error saving score:', error);
                }
            }

            // Listeners
            container.addEventListener('touchstart', handleInput, { passive: false });
            container.addEventListener('mousedown', handleInput);

            startScreen.addEventListener('click', (e) => {
                e.stopPropagation();
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

            // Prevent scroll
            container.addEventListener('touchmove', (e) => {
                if (e.cancelable) e.preventDefault();
            }, { passive: false });
        });
    </script>
@endsection