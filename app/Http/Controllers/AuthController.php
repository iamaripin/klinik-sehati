<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // --- LOGIN PAGE
    public function loginPage()
    {
        return view('auth.login');
    }

    // --- LOGIN PROCESS
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password
        ])) {
            // check status active
            if (auth()->user()->status != 'active') {
                Auth::logout();
                return back()->with('error', 'Akun Anda tidak aktif. Silakan hubungi administrator.');
            }
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }


        return back()->with('error', 'Username atau password salah');
    }

    // --- LOGOUT
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    // --- PASSWORD PAGE
    public function passwordPage()
    {
        return view('auth.password');
    }

    // --- UPDATE PASSWORD
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (! Hash::check($request->old_password, auth()->user()->password)) {
            return back()->with('error', 'Password lama salah');
        }

        auth()->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diubah');
    }
}
