@extends('layouts.mahasiswa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Profile Header Card --}}
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl shadow-lg overflow-hidden">
        <div class="p-8">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
                {{-- Profile Photo --}}
                <div class="relative">
                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                        @if($student->photo)
                            <img src="{{ Storage::url($student->photo) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-green-400 to-green-500 flex items-center justify-center">
                                <i data-lucide="user" class="w-16 h-16 text-white"></i>
                            </div>
                        @endif
                    </div>
                    <button 
                        onclick="document.getElementById('photoModal').classList.remove('hidden')"
                        class="absolute bottom-0 right-0 bg-white text-green-600 p-2 rounded-full shadow-lg hover:bg-green-50 transition"
                        title="Ubah Foto"
                    >
                        <i data-lucide="camera" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- Profile Info --}}
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-3xl font-bold text-white mb-2">{{ $student->name }}</h2>
                    <p class="text-green-100 text-lg mb-3">{{ $student->email }}</p>
                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                        <span class="px-4 py-1 bg-white bg-opacity-20 text-white rounded-full text-sm font-semibold">
                            {{ $student->student_id }}
                        </span>
                        <span class="px-4 py-1 bg-green-500 text-white rounded-full text-sm font-semibold">
                            <i data-lucide="graduation-cap" class="w-4 h-4 inline mr-1"></i>
                            {{ $student->major }}
                        </span>
                        @if($student->is_active)
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
                        <p class="text-3xl font-bold text-white">{{ $student->borrowings->count() }}</p>
                        <p class="text-green-100 text-sm">Total Peminjaman</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <p class="text-3xl font-bold text-white">{{ $student->borrowings->where('status', 'returned')->count() }}</p>
                        <p class="text-green-100 text-sm">Dikembalikan</p>
                    </div>
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
                    :class="activeTab === 'profile' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition"
                >
                    <i data-lucide="user" class="w-5 h-5 inline mr-2"></i>
                    Informasi Profil
                </button>
                <button 
                    @click="activeTab = 'password'"
                    :class="activeTab === 'password' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition"
                >
                    <i data-lucide="lock" class="w-5 h-5 inline mr-2"></i>
                    Ubah Password
                </button>
                <button 
                    @click="activeTab = 'history'"
                    :class="activeTab === 'history' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition"
                >
                    <i data-lucide="history" class="w-5 h-5 inline mr-2"></i>
                    Riwayat
                </button>
            </nav>
        </div>

        <div class="p-6">
            {{-- Profile Information Tab --}}
            <div x-show="activeTab === 'profile'" x-cloak>
                <h3 class="text-xl font-bold text-gray-800 mb-6">Edit Informasi Profil</h3>
                
                <form action="{{ route('mahasiswa.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        {{-- NIM (Read Only) --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                NIM
                            </label>
                            <input 
                                type="text" 
                                value="{{ $student->student_id }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 font-mono"
                                readonly
                            >
                            <p class="text-xs text-gray-500 mt-1">NIM tidak dapat diubah</p>
                        </div>

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
                                    value="{{ old('name', $student->name) }}"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror"
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
                                    value="{{ old('email', $student->email) }}"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
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
                                    value="{{ old('phone', $student->phone) }}"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                >
                            </div>
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jurusan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Jurusan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="graduation-cap" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    name="major"
                                    value="{{ old('major', $student->major) }}"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('major') border-red-500 @enderror"
                                    required
                                >
                            </div>
                            @error('major')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
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
                                class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold flex items-center justify-center space-x-2"
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
                
                <form action="{{ route('mahasiswa.profile.update-password') }}" method="POST">
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
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('current_password') border-red-500 @enderror"
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
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-500 @enderror"
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
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    required
                                >
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
                                class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold flex items-center justify-center space-x-2"
                            >
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                                <span>Ubah Password</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- History Tab --}}
            <div x-show="activeTab === 'history'" x-cloak>
                <h3 class="text-xl font-bold text-gray-800 mb-6">Riwayat Peminjaman Terbaru</h3>
                
                <div class="space-y-4">
                    @forelse($student->borrowings()->with('item')->latest()->take(10)->get() as $borrowing)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                    @if($borrowing->item->photo)
                                        <img src="{{ Storage::url($borrowing->item->photo) }}" alt="{{ $borrowing->item->name }}" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <i data-lucide="package" class="w-6 h-6 text-white"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $borrowing->item->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $borrowing->borrowing_code }} • {{ $borrowing->borrow_date->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                @if($borrowing->status == 'pending')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($borrowing->status == 'approved')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Dipinjam</span>
                                @elseif($borrowing->status == 'rejected')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Ditolak</span>
                                @elseif($borrowing->status == 'returned')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Dikembalikan</span>
                                @endif
                                <a href="{{ route('mahasiswa.borrowings.show', $borrowing->id) }}" class="text-green-600 hover:text-green-700">
                                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
                            <p>Belum ada riwayat peminjaman</p>
                        </div>
                    @endforelse

                    @if($student->borrowings->count() > 10)
                        <div class="text-center pt-4">
                            <a href="{{ route('mahasiswa.borrowings.index') }}" class="text-green-600 hover:text-green-700 font-semibold">
                                Lihat Semua Riwayat →
                            </a>
                        </div>
                    @endif
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
            <button onclick="document.getElementById('photoModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form action="{{ route('mahasiswa.profile.update-photo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block w-full cursor-pointer">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-green-500 transition">
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
                    onclick="document.getElementById('photoModal').classList.add('hidden')"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                >
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
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
            this.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection