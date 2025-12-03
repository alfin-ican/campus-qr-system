@extends('layouts.mahasiswa')

@section('content')
<div class="space-y-6">
    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ Auth::guard('student')->user()->name }}! 👋</h2>
                <p class="text-green-100">NIM: {{ Auth::guard('student')->user()->student_id }} | {{ Auth::guard('student')->user()->major }}</p>
            </div>
            <a href="{{ route('mahasiswa.borrowings.create') }}" class="mt-4 md:mt-0 bg-white text-green-600 px-6 py-3 rounded-lg font-semibold hover:bg-green-50 transition flex items-center space-x-2">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>Ajukan Peminjaman</span>
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i data-lucide="clipboard-list" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_borrowed'] }}</p>
            <p class="text-sm text-gray-500">Total Peminjaman</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
            <p class="text-sm text-gray-500">Menunggu Persetujuan</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
            <p class="text-sm text-gray-500">Sedang Dipinjam</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i data-lucide="package-check" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['returned'] }}</p>
            <p class="text-sm text-gray-500">Sudah Dikembalikan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Active Borrowings --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">Peminjaman Aktif</h3>
                    <a href="{{ route('mahasiswa.borrowings.index') }}" class="text-sm text-green-600 hover:text-green-700">
                        Lihat Semua →
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($activeBorrowings as $borrowing)
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-100 p-2 rounded-lg">
                                    <i data-lucide="package" class="w-5 h-5 text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $borrowing->item->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $borrowing->borrowing_code }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($borrowing->status == 'pending')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($borrowing->status == 'approved')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Dipinjam</span>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">{{ $borrowing->borrow_date->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
                        <p>Belum ada peminjaman aktif</p>
                        <a href="{{ route('mahasiswa.borrowings.create') }}" class="mt-2 inline-block text-green-600 hover:text-green-700 text-sm">
                            Ajukan Peminjaman →
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Latest Items --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">Barang Terbaru</h3>
                    <a href="{{ route('mahasiswa.items.index') }}" class="text-sm text-green-600 hover:text-green-700">
                        Lihat Semua →
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($latestItems as $item)
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                    @if($item->photo)
                                        <img src="{{ Storage::url($item->photo) }}" alt="{{ $item->name }}" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <i data-lucide="package" class="w-6 h-6 text-white"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $item->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->category }}</p>
                                </div>
                            </div>
                            <a href="{{ route('mahasiswa.borrowings.create', $item->id) }}" class="bg-green-100 text-green-600 px-3 py-1 rounded-lg text-sm hover:bg-green-200 transition">
                                Pinjam
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i data-lucide="package" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
                        <p>Belum ada barang tersedia</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('mahasiswa.items.index') }}" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition group">
            <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-200 transition">
                <i data-lucide="package" class="w-6 h-6 text-blue-600"></i>
            </div>
            <h4 class="font-semibold text-gray-900">Daftar Barang</h4>
            <p class="text-sm text-gray-500">Lihat semua barang</p>
        </a>

        <a href="{{ route('mahasiswa.scan') }}" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition group">
            <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:bg-green-200 transition">
                <i data-lucide="scan" class="w-6 h-6 text-green-600"></i>
            </div>
            <h4 class="font-semibold text-gray-900">Scan QR Code</h4>
            <p class="text-sm text-gray-500">Scan untuk detail barang</p>
        </a>

        <a href="{{ route('mahasiswa.borrowings.create') }}" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition group">
            <div class="bg-purple-100 w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:bg-purple-200 transition">
                <i data-lucide="plus-circle" class="w-6 h-6 text-purple-600"></i>
            </div>
            <h4 class="font-semibold text-gray-900">Ajukan Pinjam</h4>
            <p class="text-sm text-gray-500">Buat pengajuan baru</p>
        </a>

        <a href="{{ route('mahasiswa.borrowings.index') }}" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition group">
            <div class="bg-orange-100 w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:bg-orange-200 transition">
                <i data-lucide="history" class="w-6 h-6 text-orange-600"></i>
            </div>
            <h4 class="font-semibold text-gray-900">Riwayat</h4>
            <p class="text-sm text-gray-500">Lihat riwayat peminjaman</p>
        </a>
    </div>
</div>
@endsection