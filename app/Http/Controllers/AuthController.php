<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
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