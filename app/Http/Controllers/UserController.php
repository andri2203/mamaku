<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $currenUser = Auth::user();

        $data = [
            'title' => 'Profil ' . $currenUser->name,
            'user' => $currenUser,
        ];

        return view('profile', $data);
    }

    public function two_factor_secret(Request $request)
    {
        $validated = $request->validate([
            'two_factor_secret' => 'required|numeric|digits:6',
        ]);

        $user = User::find(Auth::id());
        $user->two_factor_secret = $validated['two_factor_secret'];
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Kode rahasia dua faktor berhasil diperbarui.');
    }

    public function two_factor_recovery(Request $request)
    {
        $validated = $request->validate([
            'two_factor_recovery' => 'required|numeric|digits:6',
        ]);

        $user = User::find(Auth::id());
        $user->two_factor_recovery_codes = $validated['two_factor_recovery'];
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Kode pemulihan dua faktor berhasil diperbarui.');
    }
}
