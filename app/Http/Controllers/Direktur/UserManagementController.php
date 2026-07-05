<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
{
    $users = User::latest()->get();

    return view(
        'direktur.users.index',
        compact('users')
    );
}

public function create()
{
    return view(
        'direktur.users.create'
    );
}

    public function store(Request $request)
    {
        // hanya direktur
        if (auth()->user()->role != 'direktur') {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',

            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],

            'role' => 'required|in:admin,direktur'
        ], [

            'password.min' =>
            'Password minimal 8 karakter.',

            'password.regex' =>
            'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(
                $request->password
            ),
            'role' => $request->role
        ]);

        return redirect()
    ->route('direktur.users')
    ->with(
        'success',
        'Akun '.$request->role.' berhasil dibuat.'
    );
    }
}
