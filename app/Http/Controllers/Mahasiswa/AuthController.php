<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Student;
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
        if (Auth::guard('student')->check()) {
            return redirect()->route('mahasiswa.dashboard');
        }

        return view('mahasiswa.auth.login');
    }

    // ===== PROSES LOGIN =====
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string|min:6',
        ], [
            'login.required' => 'NIM atau Email wajib diisi',
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

        // Cek apakah login menggunakan email atau student_id (NIM)
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';

        // Attempt login
        if (Auth::guard('student')->attempt(
            [$fieldType => $login, 'password' => $password, 'is_active' => true],
            $remember
        )) {
            $request->session()->regenerate();

            Session::put('student_logged_in', true);
            Session::put('student_login_time', now()->timestamp);

            return redirect()->intended(route('mahasiswa.dashboard'))
                ->with('success', 'Login berhasil! Selamat datang, ' . Auth::guard('student')->user()->name)
                ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        }

        return redirect()->back()
            ->withErrors(['login' => 'NIM/Email atau Password salah!'])
            ->withInput()
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }

    // ===== TAMPILKAN FORM REGISTER =====
    public function showRegisterForm()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('mahasiswa.dashboard');
        }

        return view('mahasiswa.auth.register');
    }

    // ===== PROSES REGISTER =====
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|unique:students,student_id|max:20',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'major' => 'required|string|max:100',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'student_id.required' => 'NIM wajib diisi',
            'student_id.unique' => 'NIM sudah terdaftar',
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'major.required' => 'Jurusan wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Buat mahasiswa baru
        $student = Student::create([
            'student_id' => $request->student_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'major' => $request->major,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // ✅ AUTO LOGIN setelah register
        Auth::guard('student')->login($student);

        // Regenerate session
        $request->session()->regenerate();

        // Set session flags
        Session::put('student_logged_in', true);
        Session::put('student_login_time', now()->timestamp);

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $student->name . '! NIM Anda: ' . $student->student_id)
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }

    // ===== LOGOUT =====
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Session::flush();
        Session::put('just_logged_out', true);

        return redirect()->route('mahasiswa.login')
            ->with('success', 'Logout berhasil!')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }
}