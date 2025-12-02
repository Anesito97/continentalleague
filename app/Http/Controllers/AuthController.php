<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class AuthController extends Controller
{
    // --- GOOGLE AUTH ---
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                // Check if user exists with same email
                $user = User::where('email', $googleUser->email)->first();

                if ($user) {
                    // Update existing user with google_id
                    $user->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                    ]);
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                        'password' => null, // No password for Google users
                    ]);
                }
            }

            if ($user->is_blocked) {
                return redirect()->route('home')->with('error', 'Tu cuenta ha sido bloqueada. Contacta al administrador.');
            }

            Auth::login($user);

            return redirect()->route('home');

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Error al iniciar sesión con Google.');
        }
    }
    // Función de LOGIN, usada por el formulario POST
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Intentamos autenticar usando el proveedor de la BD
        // OJO: Esto asume que has configurado un provider en config/auth.php para tu tabla 'usuarios'
        // Y que estás usando la columna 'password_hash' (si NO está hasheada) o 'password' (si usas Hash::make)

        // Dado que en tu API no usas hash, simularemos la verificación
        $user = DB::table('usuarios')
            ->where('username', $credentials['username'])
            ->where('password_hash', $credentials['password'])
            ->first();

        if ($user && $user->rol === 'admin') {
            // Laravel no puede autenticar un usuario que no usa hash.
            // Para fines de esta migración, si las credenciales son correctas, simulamos la sesión:
            session(['is_admin' => true, 'admin_username' => $user->username]);

            return redirect()->route('admin.panel')->with('success', 'Acceso concedido al panel de administración.');
        }

        return back()->withErrors(['login' => 'Credenciales incorrectas o rol no autorizado.'])->withInput();
    }

    // Función de LOGOUT
    public function logout(Request $request)
    {
        $request->session()->forget(['is_admin', 'admin_username']);

        return redirect('/')->with('success', 'Sesión cerrada correctamente.');
    }
}
