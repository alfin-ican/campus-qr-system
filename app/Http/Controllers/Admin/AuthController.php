<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ===== TAMPILKAN FORM LOGIN =====
    public function showLoginForm()
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    // ===== PROSES LOGIN =====
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string|min:6',
        ], [
            'login.required' => 'Email atau ID Admin wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->has('remember');

        // Cek apakah login menggunakan email atau admin_id
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'admin_id';

        // Attempt login
        if (Auth::guard('admin')->attempt(
            [$fieldType => $login, 'password' => $password, 'is_active' => true],
            $remember
        )) {
            // Update last login
            Auth::guard('admin')->user()->update(['last_login' => now()]);

            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            // Set session flag untuk mencegah back ke login page
            Session::put('admin_logged_in', true);
            Session::put('admin_login_time', now()->timestamp);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Login berhasil! Selamat datang, ' . Auth::guard('admin')->user()->name)
                ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        }

        return redirect()->back()
            ->withErrors(['login' => 'Email/ID Admin atau Password salah!'])
            ->withInput()
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }

    // ===== TAMPILKAN FORM REGISTER =====
    public function showRegisterForm()
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.register');
    }

    // ===== PROSES REGISTER =====
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,petugas',
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Buat admin baru
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        // ✅ AUTO LOGIN setelah register
        Auth::guard('admin')->login($admin);
        
        // Update last login
        $admin->update(['last_login' => now()]);

        // Regenerate session
        $request->session()->regenerate();

        // Set session flags
        Session::put('admin_logged_in', true);
        Session::put('admin_login_time', now()->timestamp);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $admin->name . '! ID Admin Anda: ' . $admin->admin_id)
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }

    // ===== LOGOUT =====
    public function logout(Request $request)
    {
        // Simpan informasi sebelum logout
        $logoutTime = now()->timestamp;
        
        // Logout admin
        Auth::guard('admin')->logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate token untuk keamanan
        $request->session()->regenerateToken();

        // Clear semua session data
        Session::flush();

        // Set session flag bahwa user baru saja logout
        Session::put('just_logged_out', true);
        Session::put('logout_time', $logoutTime);

        return redirect()->route('admin.login')
            ->with('success', 'Logout berhasil!')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }
}