<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Controller Autentikasi (Sanctum token-based).
 */
class AuthController extends Controller
{
    /**
     * Halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Halaman registrasi.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi.
     * Password di-hash otomatis via model cast 'hashed' (Bcrypt).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:customer,owner'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create($validated);

        Auth::login($user);

        // Buat Sanctum token
        $token = $user->createToken('auth-token')->plainTextToken;

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token,
            ], 201);
        }

        // Redirect berdasarkan role
        return match ($user->role) {
            'owner' => redirect()->route('owner.dashboard'),
            default => redirect()->route('home'),
        };
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Email atau password salah.'], 401);
            }
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        $request->session()->regenerate();

        // Buat Sanctum token untuk API
        $token = $user->createToken('auth-token')->plainTextToken;

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token,
            ]);
        }

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'owner' => redirect()->route('owner.dashboard'),
            default => redirect()->route('home'),
        };
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        // Hapus semua Sanctum tokens
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Berhasil logout.']);
        }

        return redirect()->route('home');
    }
}
