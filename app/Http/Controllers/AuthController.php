<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SecurityLog;

class AuthController extends Controller
{

    // 🔥 LOGIN
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {

        $request->session()->regenerate();
        $user = Auth::user();

        // ✅ LOG BERHASIL
        SecurityLog::create([
            'email' => $request->email,
            'status' => 'success',
            'ip_address' => $request->ip()
        ]);

        if ($user->role == 'admin') {
            return redirect('/admin');
        } elseif ($user->role == 'direktur') {
            return redirect('/direktur');
        } else {
            return redirect('/customer');
        }
    }

    // ❌ LOG GAGAL
    SecurityLog::create([
        'email' => $request->email,
        'status' => 'failed',
        'ip_address' => $request->ip()
    ]);

    return back()->with('error', 'Invalid email or password');
}


    // 🔥 REGISTER (FULL FIX)
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|string|max:20',
        'company' => 'required|string|max:255',

        // 🔥 VALIDASI PASSWORD BARU
        'password' => [
            'required',
            'min:8',
            'regex:/[0-9]/',        // angka
            'regex:/[A-Z]/',        // huruf besar
            'regex:/[@$!%*#?&]/'    // simbol
        ],

        // 🔥 CONFIRM PASSWORD
        'password_confirmation' => 'required|same:password',
    ]);

    // 🔥 SIMPAN USER
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'company' => $request->company,
        'password' => Hash::make($request->password),
        'role' => 'customer' // tetap customer
    ]);

    // 🔥 AUTO LOGIN
    Auth::login($user);

    // 🔥 REDIRECT CUSTOMER
    return redirect('/customer')->with('success', 'Register successful & logged in');
}


    //  RESET PASSWORD
    public function resetPassword(Request $request)
{
    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        return back()->with('error', 'Email not found');
    }

    // 🔥 DEBUG (CEK PASSWORD BARU)
    // dd($request->password);

    // 🔥 UPDATE PASSWORD (WAJIB HASH)
    $user->password = Hash::make($request->password);
    $user->save();

    // 🔥 CEK TERSIMPAN
    // dd($user);


    return redirect('/login')->with('success', 'Password reset successfully, please login');
}
}