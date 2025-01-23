<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login request
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect berdasarkan role
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Login sebagai Admin berhasil!');
            } elseif (Auth::user()->role === 'mahasiswa') {
                return redirect()->route('mahasiswa.dashboard')->with('success', 'Login sebagai Mahasiswa berhasil!');
            } else {
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Role tidak dikenali.']);
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    // Show register form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle register request
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'mahasiswa'; // Default role mahasiswa
        User::create($data);

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout(); // Melakukan logout
        $request->session()->invalidate(); // Menghapus semua data session
        $request->session()->regenerateToken(); // Regenerasi token CSRF

        return redirect('/login')->with('success', 'Logout berhasil!'); // Redirect ke login
    }
}