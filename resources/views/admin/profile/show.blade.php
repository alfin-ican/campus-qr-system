@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Profile Header Card --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg overflow-hidden">
        <div class="p-8">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
                {{-- Profile Photo --}}
                <div class="relative">
                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                        @if($admin->photo)
                            <img src="{{ Storage::url($admin->photo) }}" alt="{{ $admin->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                <i data-lucide="user" class="w-16 h-16 text-white"></i>
                            </div>
                        @endif
                    </div>
                    <button 
                        onclick="document.getElementById('photoModal').classList.remove('hidden')"
                        class="absolute bottom-0 right-0 bg-white text-indigo-600 p-2 rounded-full shadow-lg hover:bg-indigo-50 transition"
                        title="Ubah Foto"
                    >
                        <i data-lucide="camera" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- Profile Info --}}
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-3xl font-bold text-white mb-2">{{ $admin->name }}</h2>
                    <p class="text-indigo-100 text-lg mb-3">{{ $admin->email }}</p>
                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                        <span class="px-4 py-1 bg-white bg-opacity-20 text-white rounded-full text-sm font-semibold">
                            {{ $admin->admin_id }}
                        </span>
                        @if($admin->role == 'admin')
                            <span class="px-4 py-1 bg-yellow-400 text-yellow-900 rounded-full text-sm font-semibold">
                                <i data-lucide="crown" class="w-4 h-4 inline mr-1"></i>
                                Admin
                            </span>
                        @else
                            <span class="px-4 py-1 bg-blue-400 text-blue-900 rounded-full text-sm font-semibold">
                                <i data-lucide="shield" class="w-4 h-4 inline mr-1"></i>
                                Petugas
                            </span>
                        @endif
                        @if($admin->is_active)
                            <span class="px-4 py-1 bg-green-400 text-green-900 rounded-full text-sm font-semibold">
                                <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                                Aktif
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <p class="text-3xl font-bold text-white">{{ $admin->items->count() }}</p>
                        <p class="text-indigo-100 text-sm">Barang Ditambahkan</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <p class="text-3xl font-bold text-white">{{ $admin->approvedBorrowings->count() }}</p>
                        <p class="text-indigo-100 text-sm">Peminjaman Disetujui</p>
                    </div>
                </div>
            </div>

            {{-- Last Login --}}
            <div class="mt-6 pt-6 border-t border-indigo-400">
                <div class="flex items-center justify-between text-indigo-100">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        <span class="text-sm">Last Login:</span>
                    </div>
                    <span class="text-sm font-semibold">
                        {{ $admin->last_login ? $admin->last_login->format('d M Y, H:i') : 'Belum pernah login' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden" x-data="{ activeTab: 'profile' }">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button 
                    @click="activeTab = 'profile'"
                    :class="activeTab === 'profile' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition"
                >
                    <i data-lucide="user" class="w-5 h-5 inline mr-2"></i>
                    Informasi Profil
                </button>
                <button 
                    @click="activeTab = 'password'"
                    :class="activeTab === 'password' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition"
                >
                    <i data-lucide="lock" class="w-5 h-5 inline mr-2"></i>
                    Ubah Password
                </button>
                <button 
                    @click="activeTab = 'activity'"
                    :class="activeTab === 'activity' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition"
                >
                    <i data-lucide="activity" class="w-5 h-5 inline mr-2"></i>
                    Aktivitas
                </button>
            </nav>
        </div>

        <div class="p-6">
            {{-- Profile Information Tab --}}
            <div x-show="activeTab === 'profile'" x-cloak>
                <h3 class="text-xl font-bold text-gray-800 mb-6">Edit Informasi Profil</h3>
                
                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        {{-- Nama Lengkap --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    name="name"
                                    value="{{ old('name', $admin->name) }}"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                    required
                                >
                            </div>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <input 
                                    type="email" 
                                    name="email"
                                    value="{{ old('email', $admin->email) }}"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                    required
                                >
                            </div>
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- No. Telepon --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                No. Telepon
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="phone" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <input 
                                    type="tel" 
                                    name="phone"
                                    value="{{ old('phone', $admin->phone) }}"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                >
                            </div>
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ID Admin (Read Only) --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                ID Admin
                            </label>
                            <input 
                                type="text" 
                                value="{{ $admin->admin_id }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 font-mono"
                                readonly
                            >
                            <p class="text-xs text-gray-500 mt-1">ID Admin tidak dapat diubah</p>
                        </div>

                        {{-- Role (Read Only) --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Role
                            </label>
                            <input 
                                type="text" 
                                value="{{ $admin->role == 'admin' ? 'Admin' : 'Petugas' }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 capitalize"
                                readonly
                            >
                            <p class="text-xs text-gray-500 mt-1">Role tidak dapat diubah</p>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex space-x-3 pt-4 border-t">
                            <button 
                                type="reset"
                                class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold"
                            >
                                Reset
                            </button>
                            <button 
                                type="submit"
                                class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold flex items-center justify-center space-x-2"
                            >
                                <i data-lucide="save" class="w-5 h-5"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Change Password Tab --}}
            <div x-show="activeTab === 'password'" x-cloak>
                <h3 class="text-xl font-bold text-gray-800 mb-6">Ubah Password</h3>
                
                <form action="{{ route('admin.profile.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        {{-- Current Password --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Password Lama <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    name="current_password"
                                    placeholder="Masukkan password lama"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('current_password') border-red-500 @enderror"
                                    required
                                >
                            </div>
                            @error('current_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- New Password --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="key" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    name="password"
                                    placeholder="Minimal 6 karakter"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                    required
                                >
                            </div>
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm New Password --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Konfirmasi Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="check-circle" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    name="password_confirmation"
                                    placeholder="Ulangi password baru"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    required
                                >
                            </div>
                        </div>

                        {{-- Password Requirements --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                                <div>
                                    <p class="font-semibold text-blue-900 mb-2">Persyaratan Password:</p>
                                    <ul class="text-sm text-blue-800 space-y-1">
                                        <li class="flex items-center space-x-2">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                            <span>Minimal 6 karakter</span>
                                        </li>
                                        <li class="flex items-center space-x-2">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                            <span>Kombinasi huruf dan angka lebih aman</span>
                                        </li>
                                        <li class="flex items-center space-x-2">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                            <span>Hindari password yang mudah ditebak</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex space-x-3 pt-4 border-t">
                            <button 
                                type="reset"
                                class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit"
                                class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold flex items-center justify-center space-x-2"
                            >
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                                <span>Ubah Password</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Activity Tab --}}
            <div x-show="activeTab === 'activity'" x-cloak>
                <h3 class="text-xl font-bold text-gray-800 mb-6">Aktivitas Terbaru</h3>
                
                <div class="space-y-4">
                    {{-- Recent Items Added --}}
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-800 flex items-center space-x-2">
                                <i data-lucide="package" class="w-5 h-5 text-indigo-600"></i>
                                <span>Barang yang Ditambahkan</span>
                            </h4>
                            <span class="text-sm text-gray-500">{{ $admin->items->count() }} total</span>
                        </div>
                        
                        @if($admin->items->count() > 0)
                            <div class="space-y-2">
                                @foreach($admin->items->take(5) as $item)
                                    <div class="flex items-center justify-between bg-white p-3 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $item->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</p>
                                        </div>
                                        <a href="{{ route('admin.items.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-700">
                                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            @if($admin->items->count() > 5)
                                <a href="{{ route('admin.items.index') }}" class="block text-center text-sm text-indigo-600 hover:text-indigo-700 mt-3">
                                    Lihat Semua Barang →
                                </a>
                            @endif
                        @else
                            <p class="text-gray-500 text-center py-4">Belum ada barang yang ditambahkan</p>
                        @endif
                    </div>

                    {{-- Approved Borrowings --}}
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-800 flex items-center space-x-2">
                                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                                <span>Peminjaman yang Disetujui</span>
                            </h4>
                            <span class="text-sm text-gray-500">{{ $admin->approvedBorrowings->count() }} total</span>
                        </div>
                        
                        @if($admin->approvedBorrowings->count() > 0)
                            <div class="space-y-2">
                                @foreach($admin->approvedBorrowings->take(5) as $borrowing)
                                    <div class="flex items-center justify-between bg-white p-3 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $borrowing->student->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $borrowing->item->name }} • {{ $borrowing->approved_at->diffForHumans() }}</p>
                                        </div>
                                        <a href="{{ route('admin.borrowings.show', $borrowing->id) }}" class="text-green-600 hover:text-green-700">
                                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            @if($admin->approvedBorrowings->count() > 5)
                                <a href="{{ route('admin.borrowings.index') }}" class="block text-center text-sm text-green-600 hover:text-green-700 mt-3">
                                    Lihat Semua Peminjaman →
                                </a>
                            @endif
                        @else
                            <p class="text-gray-500 text-center py-4">Belum ada peminjaman yang disetujui</p>
                        @endif
                    </div>

                    {{-- Account Info --}}
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 border border-indigo-100">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center space-x-2">
                            <i data-lucide="info" class="w-5 h-5 text-indigo-600"></i>
                            <span>Informasi Akun</span>
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Akun dibuat:</span>
                                <span class="font-medium text-gray-900">{{ $admin->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Last login:</span>
                                <span class="font-medium text-gray-900">{{ $admin->last_login ? $admin->last_login->diffForHumans() : 'Belum pernah' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total barang:</span>
                                <span class="font-medium text-gray-900">{{ $admin->items->count() }} barang</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total persetujuan:</span>
                                <span class="font-medium text-gray-900">{{ $admin->approvedBorrowings->count() }} peminjaman</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Photo Upload Modal --}}
<div id="photoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Ubah Foto Profil</h3>
            <button onclick="closePhotoModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.profile.update-photo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block w-full cursor-pointer">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-indigo-500 transition">
                        <i data-lucide="upload" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                        <p class="text-sm text-gray-600 mb-1">Klik untuk upload foto</p>
                        <p class="text-xs text-gray-400">JPG, PNG (Max. 2MB)</p>
                    </div>
                    <input 
                        type="file" 
                        name="photo" 
                        accept="image/*" 
                        class="hidden" 
                        onchange="previewPhotoModal(event)"
                        required
                    >
                </label>
                
                <div id="photoPreviewModal" class="hidden mt-4">
                    <img id="previewModal" src="" alt="Preview" class="w-full h-64 object-cover rounded-lg">
                </div>
            </div>

            <div class="flex space-x-3">
                <button 
                    type="button"
                    onclick="closePhotoModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                >
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function closePhotoModal() {
        document.getElementById('photoModal').classList.add('hidden');
        document.getElementById('photoPreviewModal').classList.add('hidden');
    }

    function previewPhotoModal(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewModal').src = e.target.result;
                document.getElementById('photoPreviewModal').classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    // Close modal when clicking outside
    document.getElementById('photoModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closePhotoModal();
        }
    });
</script>
@endpush
@endsection