@extends('layouts.guest')

@section('content')
<div class="w-full max-w-md animate-fade-in">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-8 text-center">
            <div class="flex justify-center mb-4">
                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                    <i data-lucide="shield" class="w-12 h-12 text-white"></i>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Login Admin/Petugas</h1>
            <p class="text-indigo-100">Sistem Manajemen Barang Kampus</p>
        </div>

        {{-- Form --}}
        <div class="p-8">
            {{-- Alert Messages --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                    <span class="text-sm">{{ session('success') }}</span>
                </div>
            @endif

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

            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf

                {{-- Email or Admin ID --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Email atau ID Admin
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            name="login"
                            value="{{ old('login') }}"
                            placeholder="email@example.com atau ADM0001"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            required
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password"
                            placeholder="Masukkan password"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            required
                        >
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                    </label>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700">Lupa Password?</a>
                </div>

                {{-- Submit Button --}}
                <button 
                    type="submit"
                    class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300 flex items-center justify-center space-x-2"
                >
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    <span>Login</span>
                </button>
            </form>

            {{-- Register Link --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun? 
                    <a href="{{ route('admin.register') }}" class="text-indigo-600 font-semibold hover:text-indigo-700">
                        Daftar Sekarang
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