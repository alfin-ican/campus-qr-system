@extends('layouts.mahasiswa')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Detail Peminjaman</h2>
                    <p class="text-green-100 mt-1">{{ $borrowing->borrowing_code }}</p>
                </div>
                <a href="{{ route('mahasiswa.borrowings.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <div class="p-6 space-y-6">
            {{-- Status Badge --}}
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i data-lucide="info" class="w-6 h-6 text-gray-600"></i>
                    <span class="font-semibold text-gray-700">Status Peminjaman:</span>
                </div>
                @if($borrowing->status == 'pending')
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                        <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                        Menunggu Persetujuan
                    </span>
                @elseif($borrowing->status == 'approved')
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                        <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                        Disetujui - Sedang Dipinjam
                    </span>
                @elseif($borrowing->status == 'rejected')
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                        <i data-lucide="x-circle" class="w-4 h-4 inline mr-1"></i>
                        Ditolak
                    </span>
                @elseif($borrowing->status == 'returned')
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                        <i data-lucide="package-check" class="w-4 h-4 inline mr-1"></i>
                        Sudah Dikembalikan
                    </span>
                @elseif($borrowing->status == 'late')
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                        <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                        Terlambat Dikembalikan
                    </span>
                @endif
            </div>

            {{-- Item Information --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Barang</h3>
                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                        @if($borrowing->item->photo)
                            <img src="{{ Storage::url($borrowing->item->photo) }}" alt="{{ $borrowing->item->name }}" class="w-full h-full object-cover rounded-lg">
                        @else
                            <i data-lucide="package" class="w-10 h-10 text-white"></i>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ $borrowing->item->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $borrowing->item->item_code }}</p>
                        <p class="text-sm text-gray-500">{{ $borrowing->item->category }}</p>
                        <p class="text-sm text-gray-500">
                            <i data-lucide="map-pin" class="w-4 h-4 inline"></i>
                            {{ $borrowing->item->location ?: 'Lokasi tidak tersedia' }}
                        </p>
                    </div>
                    <a href="{{ route('mahasiswa.items.show', $borrowing->item->id) }}" class="bg-green-100 text-green-600 px-4 py-2 rounded-lg hover:bg-green-200 transition text-sm">
                        Lihat Detail
                    </a>
                </div>
            </div>

            {{-- Borrowing Details --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Peminjaman</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Kode Peminjaman</p>
                        <p class="font-mono font-semibold text-gray-900">{{ $borrowing->borrowing_code }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
                        <p class="font-semibold text-gray-900">{{ $borrowing->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Tanggal Pinjam</p>
                        <p class="font-semibold text-gray-900">{{ $borrowing->borrow_date->format('d M Y') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Rencana Pengembalian</p>
                        <p class="font-semibold text-gray-900">{{ $borrowing->planned_return_date->format('d M Y') }}</p>
                    </div>
                    @if($borrowing->return_date)
                        <div class="p-4 bg-gray-50 rounded-lg md:col-span-2">
                            <p class="text-sm text-gray-500">Tanggal Dikembalikan</p>
                            <p class="font-semibold text-gray-900">{{ $borrowing->return_date->format('d M Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Purpose --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Tujuan Peminjaman</h3>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-700">{{ $borrowing->purpose }}</p>
                </div>
            </div>

            {{-- Approval Information --}}
            @if($borrowing->approved_by)
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Persetujuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Diproses Oleh</p>
                            <p class="font-semibold text-gray-900">{{ $borrowing->approver->name }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Tanggal Diproses</p>
                            <p class="font-semibold text-gray-900">{{ $borrowing->approved_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Rejection Reason --}}
            @if($borrowing->status == 'rejected' && $borrowing->rejection_reason)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="font-semibold text-red-800 mb-2">
                        <i data-lucide="alert-circle" class="w-5 h-5 inline mr-1"></i>
                        Alasan Penolakan
                    </h3>
                    <p class="text-red-700">{{ $borrowing->rejection_reason }}</p>
                </div>
            @endif

            {{-- Late Warning --}}
            @if($borrowing->status == 'approved' && $borrowing->is_late)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="font-semibold text-red-800 mb-2">
                        <i data-lucide="alert-triangle" class="w-5 h-5 inline mr-1"></i>
                        Peringatan Keterlambatan
                    </h3>
                    <p class="text-red-700">
                        Peminjaman Anda sudah melewati tanggal pengembalian yang direncanakan. 
                        Segera kembalikan barang untuk menghindari sanksi.
                    </p>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex space-x-3 pt-4 border-t">
                @if($borrowing->status == 'pending')
                    <form action="{{ route('mahasiswa.borrowings.cancel', $borrowing->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold flex items-center justify-center space-x-2">
                            <i data-lucide="x" class="w-5 h-5"></i>
                            <span>Batalkan Pengajuan</span>
                        </button>
                    </form>
                @endif
                <a href="{{ route('mahasiswa.borrowings.index') }}" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection