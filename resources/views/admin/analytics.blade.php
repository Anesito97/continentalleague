<div class="space-y-6">
    <h2 class="text-2xl font-bold text-white mb-4">Analíticas de Visitas</h2>

    <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-gray-300">
                <thead class="bg-gray-900/50 text-xs uppercase font-bold text-gray-400">
                    <tr>
                        <th class="px-6 py-4">URL / Página</th>
                        <th class="px-6 py-4 text-center">Total Visitas</th>
                        <th class="px-6 py-4 text-right">Última Visita</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($pageViews as $view)
                        <tr class="hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4 font-mono text-sm text-blue-400">
                                <a href="/{{ $view->url }}" target="_blank" class="hover:underline">
                                    /{{ $view->url }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-white text-lg">
                                {{ number_format($view->total) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-400">
                                {{ \Carbon\Carbon::parse($view->last_visit)->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-outlined text-4xl mb-2">query_stats</span>
                                <p>No hay datos de visitas registrados aún.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>