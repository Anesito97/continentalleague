@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-center min-h-[calc(100vh-200px)]">
        <div class="bg-gray-800 card p-8 w-full max-w-md shadow-2xl modal-card-glow">
            <h4 class="text-2xl font-bold mb-6 text-green-400 text-center">Acceso de Administrador</h4>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                @if($errors->any())
                    <div class="bg-red-500/10 border border-red-500 text-red-500 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4">
                    <label for="login-username" class="block text-sm font-medium text-gray-400 mb-1">Usuario</label>
                    <input type="text" name="username" id="login-username" required autofocus
                        class="mt-1 block w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:border-primary focus:ring-primary transition-colors">
                </div>

                <div class="mb-8">
                    <label for="login-password" class="block text-sm font-medium text-gray-400 mb-1">Contraseña</label>
                    <input type="password" name="password" id="login-password" required
                        class="mt-1 block w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:border-primary focus:ring-primary transition-colors">
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-primary to-emerald-600 hover:from-primary/90 hover:to-emerald-600/90 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-glow">
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm transition-colors">
                    &larr; Volver al inicio
                </a>
            </div>
        </div>
    </div>
@endsection