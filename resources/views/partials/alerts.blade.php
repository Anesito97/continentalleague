{{-- Custom Message/Alert Modal --}}
<div id="custom-alert" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 hidden items-center justify-center">
    <div class="bg-gray-800 card p-6 w-11/12 max-w-sm border-t-4 border-green-500 shadow-2xl hover:transform-none">
        @if ($errors->any())
            <h4 id="alert-title" class="text-xl font-bold mb-3 text-red-400">Error de Validación</h4>
            <p id="alert-message" class="text-gray-300 mb-5">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </p>
        @else
            <h4 id="alert-title" class="text-xl font-bold mb-3 text-green-400"></h4>
            <p id="alert-message" class="text-gray-300 mb-5"></p>
        @endif
        <button onclick="document.getElementById('custom-alert').classList.add('hidden')"
            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition w-full">Cerrar</button>
    </div>
</div>