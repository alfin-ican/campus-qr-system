@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Detail Barang</h2>
                    <p class="text-purple-100 mt-1">Informasi lengkap barang</p>
                </div>
                <a href="{{ route('admin.items.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <div class="p-6 space-y-6">
            {{-- Image & QR Code --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Barang</label>
                    <div class="h-64 rounded-lg overflow-hidden border-2 border-gray-200">
                        @if($item->photo)
                            <img src="{{ Storage::url($item->photo) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="h-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                                <i data-lucide="package" class="w-24 h-24 text-white opacity-50"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">QR Code</label>
                    <div class="h-64 rounded-lg overflow-hidden border-2 border-gray-200 bg-white flex items-center justify-center">
                        @if($item->qr_code)
                            <img src="{{ Storage::url($item->qr_code) }}" alt="QR Code" class="max-w-full max-h-full p-4">
                        @else
                            <div class="text-center">
                                <i data-lucide="qr-code" class="w-16 h-16 text-gray-400 mx-auto mb-2"></i>
                                <p class="text-gray-500 mb-4">QR Code belum digenerate</p>
                                <form action="{{ route('admin.items.generate-qr', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                                        Generate QR Code
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    @if($item->qr_code)
                        <div class="mt-3">
                            <a href="{{ route('admin.items.download-qr', $item->id) }}" class="block w-full bg-purple-100 text-purple-600 text-center py-2 rounded-lg hover:bg-purple-200 transition">
                                Download QR Code
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Item Details --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Barang</label>
                    <p class="px-4 py-3 bg-gray-50 rounded-lg font-mono text-gray-900">{{ $item->item_code }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Barang</label>
                    <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $item->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $item->category }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-lg">
                        @if($item->status == 'tersedia')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                Tersedia
                            </span>
                        @elseif($item->status == 'dipinjam')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                Dipinjam
                            </span>
                        @elseif($item->status == 'rusak')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                Rusak
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                Maintenance
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah</label>
                    <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $item->quantity }} unit</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
                    <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $item->location ?: '-' }}</p>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                <div class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">
                    {{ $item->description ?: 'Tidak ada deskripsi' }}
                </div>
            </div>

            {{-- Created By --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dibuat Oleh</label>
                    <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $item->creator->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Dibuat</label>
                    <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $item->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            {{-- Borrowing History --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Riwayat Peminjaman</label>
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    @if($item->borrowings->count() > 0)
                        <table class="w-full">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="text-left py-2 px-4 text-xs font-semibold text-gray-600 uppercase">Mahasiswa</th>
                                    <th class="text-left py-2 px-4 text-xs font-semibold text-gray-600 uppercase">Tanggal Pinjam</th>
                                    <th class="text-left py-2 px-4 text-xs font-semibold text-gray-600 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($item->borrowings->take(5) as $borrowing)
                                    <tr>
                                        <td class="py-2 px-4 text-sm">{{ $borrowing->student->name }}</td>
                                        <td class="py-2 px-4 text-sm">{{ $borrowing->borrow_date->format('d M Y') }}</td>
                                        <td class="py-2 px-4 text-sm">
                                            @if($borrowing->status == 'pending')
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">Pending</span>
                                            @elseif($borrowing->status == 'approved')
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Disetujui</span>
                                            @elseif($borrowing->status == 'returned')
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Dikembalikan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="py-8 text-center text-gray-500">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
                            <p>Belum ada riwayat peminjaman</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex space-x-3 pt-4 border-t">
                <a href="{{ route('admin.items.edit', $item->id) }}" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center font-semibold flex items-center justify-center space-x-2">
                    <i data-lucide="edit" class="w-5 h-5"></i>
                    <span>Edit Barang</span>
                </a>
                <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold flex items-center justify-center space-x-2">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                        <span>Hapus Barang</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection