<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white">Analíticas de Visitas</h2>
        <span class="text-sm text-gray-400">Últimos 7 días</span>
    </div>

    {{-- 1. TARJETAS DE RESUMEN --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-400 text-sm font-medium">Total Visitas</h3>
                <span class="material-symbols-outlined text-blue-400">visibility</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ number_format($totalVisits) }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-400 text-sm font-medium">Visitantes Únicos</h3>
                <span class="material-symbols-outlined text-green-400">person</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ number_format($uniqueVisitors) }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-400 text-sm font-medium">Páginas Rastreadas</h3>
                <span class="material-symbols-outlined text-purple-400">pages</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $pageViews->count() }}</p>
        </div>
    </div>

    {{-- 2. GRÁFICO PRINCIPAL (Visitas por día) --}}
    <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
        <h3 class="text-lg font-bold text-white mb-4">Tendencia de Visitas</h3>
        <div class="relative h-64 w-full">
            <canvas id="visitsChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- 3. TABLA DE PÁGINAS MÁS VISITADAS --}}
        <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-700">
                <h3 class="text-lg font-bold text-white">Páginas Más Visitadas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-gray-300">
                    <thead class="bg-gray-900/50 text-xs uppercase font-bold text-gray-400">
                        <tr>
                            <th class="px-6 py-3">URL</th>
                            <th class="px-6 py-3 text-right">Visitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($pageViews as $view)
                            <tr class="hover:bg-gray-700/30 transition">
                                <td class="px-6 py-3 font-mono text-sm text-blue-400 truncate max-w-xs">
                                    <a href="/{{ $view->url }}" target="_blank" class="hover:underline">
                                        /{{ $view->url }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-white">
                                    {{ number_format($view->total) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                                    No hay datos aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 4. GRÁFICOS DE DISPOSITIVOS --}}
        <div class="space-y-6">
            <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                <h3 class="text-lg font-bold text-white mb-4">Navegadores</h3>
                <div class="relative h-48">
                    <canvas id="browsersChart"></canvas>
                </div>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                <h3 class="text-lg font-bold text-white mb-4">Sistemas Operativos</h3>
                <div class="relative h-48">
                    <canvas id="osChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPTS DE CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración común para modo oscuro
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.borderColor = '#374151';

    // 1. Gráfico de Visitas (Línea)
    const ctxVisits = document.getElementById('visitsChart').getContext('2d');
    new Chart(ctxVisits, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'Visitas',
                data: @json($chartData['data']),
                borderColor: '#10b981', // Primary color
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10b981',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Gráfico de Navegadores (Doughnut)
    const ctxBrowsers = document.getElementById('browsersChart').getContext('2d');
    new Chart(ctxBrowsers, {
        type: 'doughnut',
        data: {
            labels: Object.keys(@json($browserStats)),
            datasets: [{
                data: Object.values(@json($browserStats)),
                backgroundColor: ['#3b82f6', '#f97316', '#10b981', '#06b6d4', '#6b7280'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            },
            cutout: '70%'
        }
    });

    // 3. Gráfico de SO (Doughnut)
    const ctxOs = document.getElementById('osChart').getContext('2d');
    new Chart(ctxOs, {
        type: 'doughnut',
        data: {
            labels: Object.keys(@json($osStats)),
            datasets: [{
                data: Object.values(@json($osStats)),
                backgroundColor: ['#8b5cf6', '#ec4899', '#eab308', '#22c55e', '#ef4444', '#6b7280'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            },
            cutout: '70%'
        }
    });
</script>