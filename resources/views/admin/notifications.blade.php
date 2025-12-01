<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white">Notificaciones Predefinidas</h2>
        <button
            onclick="document.getElementById('create-notification-modal').classList.remove('hidden'); document.getElementById('create-notification-modal').classList.add('flex');"
            class="bg-primary hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-lg hover:shadow-glow flex items-center gap-2">
            <span class="material-symbols-outlined">add</span>
            Nueva Notificación
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($messages as $message)
            <div
                class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700 hover:border-primary/50 transition-colors group relative">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-white">{{ $message->title }}</h3>
                    <div class="flex gap-2">
                        <button
                            onclick="openEditNotificationModal({{ $message->id }}, '{{ $message->title }}', `{{ $message->content }}`)"
                            class="text-gray-400 hover:text-blue-400 transition">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <form action="{{ route('admin.notifications.destroy', $message) }}" method="POST"
                            onsubmit="return confirm('¿Estás seguro de eliminar esta notificación?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-400 transition">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div
                    class="bg-gray-900/50 p-4 rounded-lg mb-4 text-gray-300 text-sm whitespace-pre-wrap font-mono border border-gray-700/50">
                    {{ $message->content }}
                </div>

                <form action="{{ route('admin.notifications.send', $message) }}" method="POST"
                    onsubmit="return confirm('¿Estás seguro de enviar esta notificación al grupo de WhatsApp?');">
                    @csrf
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-lg transition shadow-md hover:shadow-blue-500/20 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">send</span>
                        Enviar Ahora
                    </button>
                </form>
            </div>
        @empty
            <div
                class="col-span-full text-center py-12 text-gray-500 bg-gray-800/50 rounded-xl border border-dashed border-gray-700">
                <span class="material-symbols-outlined text-4xl mb-2">notifications_off</span>
                <p>No hay notificaciones creadas aún.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- MODAL CREAR --}}
<div id="create-notification-modal"
    class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-gray-800 card p-6 w-full max-w-lg shadow-2xl modal-card-glow">
        <h3 class="text-xl font-bold mb-4 text-white">Nueva Notificación</h3>
        <form action="{{ route('admin.notifications.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-400 mb-1">Título (Interno)</label>
                <input type="text" name="title" required
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-primary focus:ring-primary">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-400 mb-1">Contenido del Mensaje</label>
                <textarea name="content" rows="5" required
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-primary focus:ring-primary font-mono text-sm"></textarea>
                <p class="text-xs text-gray-500 mt-1">Puedes usar formato de WhatsApp: *negrita*, _cursiva_, ~tachado~.
                </p>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button"
                    onclick="document.getElementById('create-notification-modal').classList.add('hidden'); document.getElementById('create-notification-modal').classList.remove('flex');"
                    class="px-4 py-2 text-gray-300 hover:text-white transition">Cancelar</button>
                <button type="submit"
                    class="bg-primary hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-lg">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR --}}
<div id="edit-notification-modal"
    class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-gray-800 card p-6 w-full max-w-lg shadow-2xl modal-card-glow">
        <h3 class="text-xl font-bold mb-4 text-white">Editar Notificación</h3>
        <form id="edit-notification-form" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-400 mb-1">Título (Interno)</label>
                <input type="text" name="title" id="edit-title" required
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-primary focus:ring-primary">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-400 mb-1">Contenido del Mensaje</label>
                <textarea name="content" id="edit-content" rows="5" required
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-primary focus:ring-primary font-mono text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button"
                    onclick="document.getElementById('edit-notification-modal').classList.add('hidden'); document.getElementById('edit-notification-modal').classList.remove('flex');"
                    class="px-4 py-2 text-gray-300 hover:text-white transition">Cancelar</button>
                <button type="submit"
                    class="bg-primary hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg transition shadow-lg">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditNotificationModal(id, title, content) {
        const modal = document.getElementById('edit-notification-modal');
        const form = document.getElementById('edit-notification-form');
        const titleInput = document.getElementById('edit-title');
        const contentInput = document.getElementById('edit-content');

        form.action = `/admin/notifications/${id}`;
        titleInput.value = title;
        contentInput.value = content;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
</script>