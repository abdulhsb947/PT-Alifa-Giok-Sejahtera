<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'photo' => 'nullable|image|max:2048'
        ]);

        // UPLOAD FOTO
        if ($request->hasFile('photo')) {

            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('public/profile', $filename);

            $user->photo = $filename;
        }


        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone ?? null;
        $user->company = $request->company ?? null;
        $user->address = $request->address ?? null;


        $user->save();

        return back()->with('success', 'Profile berhasil diperbarui');
    }

    // ======================
    // UPDATE PASSWORD
    // ======================
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        // 🔒 proteksi admin
        if ($user->role !== 'admin') {
            abort(403);
        }

        // ✅ VALIDASI
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        // ❗️CEK PASSWORD LAMA
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Kata sandi lama salah');
        }

        // 🔥 UPDATE PASSWORD (AMAN)
        $user->password = Hash::make($request->password);
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->save();

        return back()->with('success', 'Kata sandi berhasil diubah');
    }
}
