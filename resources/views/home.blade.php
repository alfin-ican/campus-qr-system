<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Sistem Manajemen Barang Kampus</title>
    
    {{-- TAILWIND CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body class="antialiased">
    {{-- Check if authenticated and redirect --}}
    @if(Auth::guard('admin')->check())
        <script>
            window.location.href = "{{ route('admin.dashboard') }}";
        </script>
    @elseif(Auth::guard('student')->check())
        <script>
            window.location.href = "{{ route('mahasiswa.dashboard') }}";
        </script>
    @endif

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        
        {{-- NAVIGATION BAR --}}
        <nav class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-3">
                        <div class="bg-indigo-600 p-2 rounded-lg">
                            <i data-lucide="qr-code" class="w-6 h-6 text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-800">CampusQR</span>
                    </div>
                    
                    {{-- Desktop Menu --}}
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="#features" class="text-gray-600 hover:text-indigo-600 transition">Fitur</a>
                        <a href="#about" class="text-gray-600 hover:text-indigo-600 transition">Tentang</a>
                        <a href="#contact" class="text-gray-600 hover:text-indigo-600 transition">Kontak</a>
                    </div>

                    {{-- Mobile Menu Button --}}
                    <button onclick="toggleMobileMenu()" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                        <i data-lucide="menu" id="menu-icon" class="w-6 h-6"></i>
                        <i data-lucide="x" id="close-icon" class="w-6 h-6 hidden"></i>
                    </button>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
                <div class="px-4 py-3 space-y-2">
                    <a href="#features" class="block py-2 text-gray-600 hover:text-indigo-600">Fitur</a>
                    <a href="#about" class="block py-2 text-gray-600 hover:text-indigo-600">Tentang</a>
                    <a href="#contact" class="block py-2 text-gray-600 hover:text-indigo-600">Kontak</a>
                </div>
            </div>
        </nav>

        {{-- HERO SECTION --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
            <div class="text-center mb-12 lg:mb-16 animate-fade-in">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-4 lg:mb-6">
                    Sistem Manajemen Barang Kampus
                </h1>
                <p class="text-xl sm:text-2xl text-indigo-600 font-semibold mb-4">
                    dengan QR Code & Status Real-Time
                </p>
                <p class="text-base sm:text-lg text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Kelola inventaris barang kampus dengan mudah, cepat, dan akurat menggunakan teknologi QR Code. 
                    Pantau status barang secara real-time dan tingkatkan efisiensi manajemen aset kampus Anda.
                </p>
            </div>

            {{-- ILLUSTRATION SECTION --}}
            <div class="flex justify-center mb-12 lg:mb-16">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 lg:gap-8 max-w-4xl">
                    <div class="bg-white p-6 lg:p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="bg-indigo-100 w-16 h-16 lg:w-20 lg:h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="qr-code" class="w-8 h-8 lg:w-10 lg:h-10 text-indigo-600"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-2">QR Code Scanner</h3>
                        <p class="text-sm lg:text-base text-gray-600">Scan barang dengan cepat menggunakan QR Code</p>
                    </div>
                    
                    <div class="bg-white p-6 lg:p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="bg-green-100 w-16 h-16 lg:w-20 lg:h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="package" class="w-8 h-8 lg:w-10 lg:h-10 text-green-600"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-2">Tracking Real-Time</h3>
                        <p class="text-sm lg:text-base text-gray-600">Pantau status dan lokasi barang secara langsung</p>
                    </div>
                    
                    <div class="bg-white p-6 lg:p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="bg-purple-100 w-16 h-16 lg:w-20 lg:h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="users" class="w-8 h-8 lg:w-10 lg:h-10 text-purple-600"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-2">Multi-User Access</h3>
                        <p class="text-sm lg:text-base text-gray-600">Akses untuk Admin, Petugas, dan Mahasiswa</p>
                    </div>
                </div>
            </div>

            {{-- LOGIN/REGISTER CARDS SECTION --}}
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-900 mb-8 lg:mb-12">
                    Pilih Role untuk Masuk ke Sistem
                </h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                    
                    {{-- CARD ADMIN/PETUGAS --}}
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6 lg:p-8">
                            <div class="flex items-center justify-center mb-4">
                                <div class="bg-white bg-opacity-20 p-3 lg:p-4 rounded-full">
                                    <i data-lucide="shield" class="w-10 h-10 lg:w-12 lg:h-12 text-white"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl lg:text-3xl font-bold text-white text-center mb-2">
                                Admin / Petugas
                            </h3>
                            <p class="text-indigo-100 text-center text-sm lg:text-base">
                                Kelola sistem dan inventaris barang kampus
                            </p>
                        </div>
                        
                        <div class="p-6 lg:p-8">
                            <ul class="space-y-3 mb-6 lg:mb-8">
                                <li class="flex items-start">
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-indigo-600 mr-2 flex-shrink-0 mt-0.5"></i>
                                    <span class="text-gray-700 text-sm lg:text-base">Kelola data barang dan kategori</span>
                                </li>
                                <li class="flex items-start">
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-indigo-600 mr-2 flex-shrink-0 mt-0.5"></i>
                                    <span class="text-gray-700 text-sm lg:text-base">Generate dan cetak QR Code</span>
                                </li>
                                <li class="flex items-start">
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-indigo-600 mr-2 flex-shrink-0 mt-0.5"></i>
                                    <span class="text-gray-700 text-sm lg:text-base">Monitor status barang real-time</span>
                                </li>
                                <li class="flex items-start">
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-indigo-600 mr-2 flex-shrink-0 mt-0.5"></i>
                                    <span class="text-gray-700 text-sm lg:text-base">Kelola peminjaman dan pengembalian</span>
                                </li>
                            </ul>
                            
                            <div class="space-y-3">
                                <a href="{{ route('admin.login') }}" 
                                   class="block w-full bg-indigo-600 text-white text-center py-3 lg:py-4 rounded-xl font-semibold hover:bg-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg text-sm lg:text-base">
                                    Login sebagai Admin/Petugas
                                </a>
                                
                                <a href="{{ route('admin.register') }}" 
                                   class="block w-full bg-white text-indigo-600 border-2 border-indigo-600 text-center py-3 lg:py-4 rounded-xl font-semibold hover:bg-indigo-50 transition-all duration-300 text-sm lg:text-base">
                                    Registrasi Admin/Petugas
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- CARD MAHASISWA --}}
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="bg-gradient-to-r from-green-600 to-green-700 p-6 lg:p-8">
                            <div class="flex items-center justify-center mb-4">
                                <div class="bg-white bg-opacity-20 p-3 lg:p-4 rounded-full">
                                    <i data-lucide="users" class="w-10 h-10 lg:w-12 lg:h-12 text-white"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl lg:text-3xl font-bold text-white text-center mb-2">
                                Mahasiswa
                            </h3>
                            <p class="text-green-100 text-center text-sm lg:text-base">
                                Akses untuk peminjaman barang kampus
                            </p>
                        </div>
                        
                        <div class="p-6 lg:p-8">
                            <ul class="space-y-3 mb-6 lg:mb-8">
                                <li class="flex items-start">
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-green-600 mr-2 flex-shrink-0 mt-0.5"></i>
                                    <span class="text-gray-700 text-sm lg:text-base">Lihat daftar barang tersedia</span>
                                </li>
                                <li class="flex items-start">
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-green-600 mr-2 flex-shrink-0 mt-0.5"></i>
                                    <span class="text-gray-700 text-sm lg:text-base">Ajukan peminjaman barang</span>
                                </li>
                                <li class="flex items-start">
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-green-600 mr-2 flex-shrink-0 mt-0.5"></i>
                                    <span class="text-gray-700 text-sm lg:text-base">Cek status peminjaman</span>
                                </li>
                                <li class="flex items-start">
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-green-600 mr-2 flex-shrink-0 mt-0.5"></i>
                                    <span class="text-gray-700 text-sm lg:text-base">Riwayat peminjaman barang</span>
                                </li>
                            </ul>
                            
                            <div class="space-y-3">
                                <a href="{{ route('mahasiswa.login') }}" 
                                   class="block w-full bg-green-600 text-white text-center py-3 lg:py-4 rounded-xl font-semibold hover:bg-green-700 transition-all duration-300 shadow-md hover:shadow-lg text-sm lg:text-base">
                                    Login sebagai Mahasiswa
                                </a>
                                
                                <a href="{{ route('mahasiswa.register') }}" 
                                   class="block w-full bg-white text-green-600 border-2 border-green-600 text-center py-3 lg:py-4 rounded-xl font-semibold hover:bg-green-50 transition-all duration-300 text-sm lg:text-base">
                                    Registrasi Mahasiswa
                                </a>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <footer class="bg-gray-900 text-white mt-16 lg:mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="bg-indigo-600 p-2 rounded-lg">
                                <i data-lucide="qr-code" class="w-5 h-5 text-white"></i>
                            </div>
                            <span class="text-lg font-bold">CampusQR</span>
                        </div>
                        <p class="text-gray-400 text-sm">
                            Sistem manajemen barang kampus yang modern dan efisien
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-3">Link Cepat</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#features" class="text-gray-400 hover:text-white transition">Fitur</a></li>
                            <li><a href="#about" class="text-gray-400 hover:text-white transition">Tentang</a></li>
                            <li><a href="#contact" class="text-gray-400 hover:text-white transition">Kontak</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-3">Kontak</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li>Email: alfinihsan27@iet.student.pens.ac.id</li>
                            <li>Telp: (+62) 856-4199-5098</li>
                            <li>Kampus: Politeknik Elektronika Negeri Surabaya, Jawa Timur</li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-8 pt-6 text-center text-sm text-gray-400">
                    <p>&copy; 2025 CampusQR. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    {{-- ===== JAVASCRIPT FOR LOCKING HOMEPAGE ===== --}}
    <script>
        lucide.createIcons();

        // Toggle Mobile Menu
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');
            
            mobileMenu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // ===== LOCK HOMEPAGE - PREVENT BACK BUTTON =====
        (function () {

            // Mendorong state baru ke history (agar back akan kembali ke state ini)
            window.history.pushState(null, "", window.location.href);

            // Ketika user menekan tombol back
            window.onpopstate = function () {
                window.history.pushState(null, "", window.location.href);
            };

        })();
    </script>
</body>
</html>
