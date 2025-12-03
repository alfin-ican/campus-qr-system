<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>
    
    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    
    {{-- Alpine.js untuk interaktivitas --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased bg-gray-100" x-data="{ sidebarOpen: false }">
    
    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform transition-transform duration-300"
        >
            <div class="flex flex-col h-full">
                {{-- Logo --}}
                <div class="flex items-center justify-between p-6 border-b border-gray-700">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                        <div class="bg-indigo-600 p-2 rounded-lg">
                            <i data-lucide="qr-code" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xl font-bold">CampusQR</span>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.items.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.items.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <i data-lucide="package" class="w-5 h-5"></i>
                        <span class="font-medium">Data Barang</span>
                    </a>

                    <a href="{{ route('admin.borrowings.index') }}" 
                       class="flex items-center justify-between px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.borrowings.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                            <span class="font-medium">Peminjaman</span>
                        </div>
                        @php
                            $pendingCount = \App\Models\Borrowing::where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.reports.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        <span class="font-medium">Laporan</span>
                    </a>

                    <a href="{{ route('admin.users.admins.index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        <span class="font-medium">Manajemen User</span>
                    </a>

                    <a href="{{ route('admin.profile.show') }}" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.profile.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                        <i data-lucide="settings" class="w-5 h-5"></i>
                        <span class="font-medium">Pengaturan</span>
                    </a>
                </nav>

                {{-- User Profile --}}
                <div class="p-4 border-t border-gray-700">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-indigo-600 p-2 rounded-full">
                            @if(Auth::guard('admin')->user()->photo)
                                <img src="{{ Storage::url(Auth::guard('admin')->user()->photo) }}" 
                                     alt="Photo" 
                                     class="w-10 h-10 rounded-full object-cover">
                            @else
                                <i data-lucide="user" class="w-5 h-5"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ Auth::guard('admin')->user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Auth::guard('admin')->user()->email }}</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span class="text-sm">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Overlay --}}
        <div 
            x-show="sidebarOpen" 
            @click="sidebarOpen = false"
            x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
        ></div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-h-screen">
            {{-- Top Bar --}}
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="flex items-center justify-between px-4 lg:px-8 py-4">
                    <div class="flex items-center space-x-4">
                        <button 
                            @click="sidebarOpen = true"
                            class="lg:hidden p-2 hover:bg-gray-100 rounded-lg"
                        >
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>
                        <h1 class="text-xl lg:text-2xl font-bold text-gray-800">
                            {{ $title ?? 'Dashboard' }}
                        </h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        {{-- Search Bar --}}
                        <div class="hidden md:flex items-center bg-gray-100 rounded-lg px-4 py-2">
                            <i data-lucide="search" class="w-5 h-5 text-gray-400 mr-2"></i>
                            <input 
                                type="text" 
                                placeholder="Cari barang..." 
                                class="bg-transparent outline-none text-sm w-48"
                            >
                        </div>

                        {{-- ===== NOTIFICATIONS DROPDOWN (FIXED) ===== --}}
                        <div x-data="{ 
                            open: false, 
                            notifications: [], 
                            unreadCount: 0,
                            loading: false,
                            intervalId: null,
                            
                            async loadNotifications() {
                                this.loading = true;
                                try {
                                    const response = await fetch('{{ route('admin.notifications.get') }}', {
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    });
                                    
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    
                                    const data = await response.json();
                                    this.notifications = data.notifications || [];
                                    this.unreadCount = data.unread_count || 0;
                                } catch (error) {
                                    console.error('Error loading notifications:', error);
                                    // Set default values on error
                                    this.notifications = [];
                                    this.unreadCount = 0;
                                } finally {
                                    this.loading = false;
                                }
                            },
                            
                            async markAllAsRead() {
                                try {
                                    const response = await fetch('{{ route('admin.notifications.mark-all-read') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    });
                                    
                                    if (response.ok) {
                                        await this.loadNotifications();
                                    }
                                } catch (error) {
                                    console.error('Error:', error);
                                }
                            },
                            
                            formatTime(dateString) {
                                try {
                                    const date = new Date(dateString);
                                    const now = new Date();
                                    const seconds = Math.floor((now - date) / 1000);
                                    
                                    if (seconds < 60) return 'Baru saja';
                                    const minutes = Math.floor(seconds / 60);
                                    if (minutes < 60) return `${minutes} menit lalu`;
                                    const hours = Math.floor(minutes / 60);
                                    if (hours < 24) return `${hours} jam lalu`;
                                    const days = Math.floor(hours / 24);
                                    if (days < 7) return `${days} hari lalu`;
                                    return date.toLocaleDateString('id-ID');
                                } catch (error) {
                                    return '';
                                }
                            },
                            
                            startPolling() {
                                // Load immediately
                                this.loadNotifications();
                                // Then poll every 30 seconds
                                this.intervalId = setInterval(() => this.loadNotifications(), 30000);
                            },
                            
                            stopPolling() {
                                if (this.intervalId) {
                                    clearInterval(this.intervalId);
                                    this.intervalId = null;
                                }
                            }
                        }" 
                        x-init="startPolling()"
                        @destroy="stopPolling()"
                        class="relative">
                            {{-- Bell Button --}}
                            <button 
                                @click="open = !open; if(open) loadNotifications()"
                                class="relative p-2 hover:bg-gray-100 rounded-lg transition"
                            >
                                <i data-lucide="bell" class="w-6 h-6 text-gray-600"></i>
                                <span 
                                    x-show="unreadCount > 0" 
                                    x-text="unreadCount > 99 ? '99+' : unreadCount"
                                    class="absolute top-0 right-0 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full min-w-[20px] text-center"
                                    x-cloak
                                ></span>
                            </button>

                            {{-- Dropdown --}}
                            <div 
                                x-show="open" 
                                @click.away="open = false"
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
                            >
                                {{-- Header --}}
                                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Notifikasi</h3>
                                        <p class="text-xs text-gray-500" x-text="`${unreadCount} belum dibaca`"></p>
                                    </div>
                                    <button 
                                        @click="markAllAsRead()"
                                        class="text-xs text-indigo-600 hover:text-indigo-800"
                                        x-show="unreadCount > 0"
                                        x-cloak
                                    >
                                        Tandai semua dibaca
                                    </button>
                                </div>

                                {{-- Loading --}}
                                <div x-show="loading" class="p-8 text-center" x-cloak>
                                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                                </div>

                                {{-- Notification List --}}
                                <div x-show="!loading" class="max-h-96 overflow-y-auto" x-cloak>
                                    <template x-if="notifications.length === 0">
                                        <div class="p-8 text-center text-gray-500">
                                            <i data-lucide="bell-off" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
                                            <p>Tidak ada notifikasi</p>
                                        </div>
                                    </template>

                                    <template x-for="notif in notifications" :key="notif.id">
                                        <a 
                                            :href="`/notifications/${notif.id}/read`"
                                            class="block p-4 hover:bg-gray-50 transition border-b border-gray-100 last:border-0"
                                            :class="!notif.is_read ? 'bg-blue-50' : ''"
                                        >
                                            <div class="flex space-x-3">
                                                <div 
                                                    class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                                                    :class="{
                                                        'bg-blue-100 text-blue-600': notif.type === 'peminjaman_baru',
                                                        'bg-green-100 text-green-600': notif.type === 'approve',
                                                        'bg-red-100 text-red-600': notif.type === 'tolak',
                                                        'bg-yellow-100 text-yellow-600': notif.type === 'peringatan_pengembalian'
                                                    }"
                                                >
                                                    <i :data-lucide="notif.icon" class="w-5 h-5"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-medium text-sm text-gray-900 truncate" x-text="notif.title"></p>
                                                    <p class="text-sm text-gray-600 line-clamp-2" x-text="notif.message"></p>
                                                    <p class="text-xs text-gray-500 mt-1" x-text="formatTime(notif.created_at)"></p>
                                                </div>
                                                <div x-show="!notif.is_read" class="flex-shrink-0" x-cloak>
                                                    <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                                </div>
                                            </div>
                                        </a>
                                    </template>
                                </div>

                                {{-- Footer --}}
                                <div class="p-3 border-t border-gray-200">
                                    <a 
                                        href="{{ route('admin.notifications.index') }}" 
                                        class="block text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                                    >
                                        Lihat Semua Notifikasi
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- ===== END NOTIFICATIONS DROPDOWN ===== --}}
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-4 lg:p-8">
                {{-- Alert Messages --}}
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-green-700">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
                        <div class="flex items-center">
                            <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-700">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <div class="flex items-start">
                            <i data-lucide="alert-triangle" class="w-5 h-5 mr-2 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="font-semibold mb-2">Terjadi kesalahan:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li class="text-sm">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="bg-white border-t border-gray-200 py-4 px-8">
                <p class="text-center text-sm text-gray-600">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </footer>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Debounced icon refresh untuk mencegah infinite loop
        document.addEventListener('alpine:initialized', () => {
            let iconTimeout;
            let isRefreshing = false;
            
            const refreshIcons = () => {
                if (isRefreshing) return;
                
                clearTimeout(iconTimeout);
                iconTimeout = setTimeout(() => {
                    isRefreshing = true;
                    lucide.createIcons();
                    setTimeout(() => {
                        isRefreshing = false;
                    }, 100);
                }, 150);
            };
            
            const observer = new MutationObserver((mutations) => {
                // Hanya refresh jika ada perubahan signifikan
                const shouldRefresh = mutations.some(mutation => {
                    return mutation.addedNodes.length > 0 || 
                           (mutation.type === 'attributes' && mutation.attributeName === 'data-lucide');
                });
                
                if (shouldRefresh) {
                    refreshIcons();
                }
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['data-lucide']
            });
            
            // Initial refresh
            setTimeout(() => lucide.createIcons(), 100);
        });
    </script>

    @stack('scripts')
    
    <script>
        // Prevent back button after logout
        (function() {
            if (window.history && window.history.pushState) {
                window.history.pushState('forward', null, window.location.href);
                
                window.onpopstate = function() {
                    window.history.pushState('forward', null, window.location.href);
                };
            }
        })();

        // Clear cache on unload
        window.addEventListener('beforeunload', function() {
            if (performance.navigation.type === 2) {
                location.reload(true);
            }
        });

        // Disable F5 and Ctrl+R after logout
        document.addEventListener('keydown', function(e) {
            if ((e.key === 'F5') || (e.ctrlKey && e.key === 'r')) {
                if (sessionStorage.getItem('justLoggedOut') === 'true') {
                    e.preventDefault();
                    location.reload(true);
                }
            }
        });
    </script>
</body>
</html>