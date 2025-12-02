<div id="users-content" class="admin-content space-y-6">
    <div class="card p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h4 class="text-2xl font-semibold text-green-400 mb-4 md:mb-0">Gestión de Usuarios (Google)</h4>

            {{-- Search Form --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="w-full md:w-1/3">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por nombre o email..."
                        class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg py-2 px-4 pl-10 focus:outline-none focus:border-green-500">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400">search</span>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                            Usuario
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                            Email
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                            Google ID
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-600 bg-gray-800 text-sm">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10">
                                        <img class="w-full h-full rounded-full object-cover"
                                            src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                                            alt="{{ $user->name }}" />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-white whitespace-no-wrap font-medium">
                                            {{ $user->name }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-600 bg-gray-800 text-sm">
                                <p class="text-gray-300 whitespace-no-wrap">{{ $user->email }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-600 bg-gray-800 text-sm">
                                <code
                                    class="text-xs bg-gray-900 px-2 py-1 rounded text-gray-400">{{ $user->google_id }}</code>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-600 bg-gray-800 text-sm">
                                <span
                                    class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $user->is_blocked ? 'text-red-300' : 'text-green-300' }}">
                                    <span aria-hidden="true"
                                        class="absolute inset-0 {{ $user->is_blocked ? 'bg-red-900' : 'bg-green-900' }} opacity-50 rounded-full"></span>
                                    <span class="relative">{{ $user->is_blocked ? 'Bloqueado' : 'Activo' }}</span>
                                </span>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-600 bg-gray-800 text-sm">
                                <form action="{{ route('admin.users.toggle-block', $user) }}" method="POST"
                                    onsubmit="return confirm('¿Estás seguro de realizar esta acción?');">
                                    @csrf
                                    <button type="submit"
                                        class="{{ $user->is_blocked ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white font-bold py-1 px-3 rounded text-xs transition">
                                        {{ $user->is_blocked ? 'Desbloquear' : 'Bloquear' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="px-5 py-5 border-b border-gray-600 bg-gray-800 text-sm text-center text-gray-400">
                                No se encontraron usuarios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div
            class="px-5 py-5 bg-gray-800 border-t border-gray-600 flex flex-col xs:flex-row items-center xs:justify-between">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>