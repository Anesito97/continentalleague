<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    use \App\Traits\LoadsCommonData;

    public function index(Request $request)
    {
        session(['activeAdminContent' => 'users']);

        $data = $this->loadAllData();

        $query = User::whereNotNull('google_id');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $data['users'] = $query->paginate(15);
        $data['activeView'] = 'admin';

        return view('index', $data);
    }

    public function toggleBlock(User $user)
    {
        $user->update([
            'is_blocked' => !$user->is_blocked
        ]);

        $status = $user->is_blocked ? 'bloqueado' : 'desbloqueado';
        return back()->with('success', "Usuario {$user->name} ha sido {$status}.");
    }
}
