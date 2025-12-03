@extends('layouts.guest')

@section('content')
<div class="w-full max-w-md animate-fade-in">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-8 text-center">
            <div class="flex justify-center mb-4">
                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                    <i data-lucide="user-plus" class="w-12 h-12 text-white"></i>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Registrasi Admin/Petugas</h1>
            <p class="text-indigo-100">Buat akun baru untuk akses sistem</p>
        </div>

        {{-- Form --}}
        <div class="p-8">
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <div class="flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2 mt-0.5"></i>
                        <div class="flex-1">
                            <ul class="text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.register.post') }}" method="POST">
                @csrf

                {{-- Nama Lengkap --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        required
                    >
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="email@example.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        required
                    >
                </div>

                {{-- No. Telepon --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">
                        No. Telepon
                    </label>
                    <input 
                        type="tel" 
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                </div>

                {{-- Role --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="role"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        required
                    >
                        <option value="">Pilih Role</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                    </select>
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password"
                        placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        required
                    >
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password_confirmation"
                        placeholder="Ulangi password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        required
                    >
                </div>

                {{-- Submit Button --}}
                <button 
                    type="submit"
                    class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300 flex items-center justify-center space-x-2"
                >
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                    <span>Daftar Sekarang</span>
                </button>
            </form>

            {{-- Login Link --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Sudah punya akun? 
                    <a href="{{ route('admin.login') }}" class="text-indigo-600 font-semibold hover:text-indigo-700">
                        Login Sekarang
                    </a>
                </p>
            </div>

            {{-- Back to Home --}}
            <div class="mt-4 text-center">
                <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center justify-center space-x-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection