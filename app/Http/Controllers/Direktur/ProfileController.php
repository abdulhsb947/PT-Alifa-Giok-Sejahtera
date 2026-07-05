<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        // 🔒 hanya direktur
        if ($user->role !== 'direktur') {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'photo' => 'nullable|image|max:2048'
        ]);

        // upload foto
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/profile', $filename);

            $user->photo = $filename;
        }

        // update data
        /** @var \App\Models\User $user */
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone ?? null;
        $user->company = $request->company ?? null;
        $user->address = $request->address ?? null;

        $user->save();

        return back()->with('success', 'Profil direktur berhasil diperbarui');
    }

    // ======================
    // UPDATE PASSWORD
    // ======================
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'direktur') {
            abort(403);
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Kata sandi lama salah');
        }
        
        /** @var \App\Models\User $user */
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Kata sandi berhasil diubah');
    }
}