@extends('layouts.mahasiswa')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Detail Barang</h2>
                    <p class="text-green-100 mt-1">{{ $item->item_code }}</p>
                </div>
                <a href="{{ route('mahasiswa.items.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Image --}}
                <div>
                    <div class="h-64 rounded-lg overflow-hidden border-2 border-gray-200">
                        @if($item->photo)
                            <img src="{{ Storage::url($item->photo) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="h-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                                <i data-lucide="package" class="w-24 h-24 text-white opacity-50"></i>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info --}}
                <div class="space-y-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $item->name }}</h3>
                        <p class="text-gray-500">{{ $item->category }}</p>
                    </div>

                    <div class="flex items-center space-x-3">
                        @if($item->status == 'tersedia')
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                                Tersedia
                            </span>
                        @elseif($item->status == 'dipinjam')
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                                Sedang Dipinjam
                            </span>
                        @elseif($item->status == 'rusak')
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                                Rusak
                            </span>
                        @else
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                <i data-lucide="wrench" class="w-4 h-4 inline mr-1"></i>
                                Maintenance
                            </span>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Kode Barang</span>
                            <span class="font-mono font-semibold">{{ $item->item_code }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah Total</span>
                            <span class="font-semibold">{{ $item->quantity }} unit</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tersedia</span>
                            <span class="font-semibold text-green-600">{{ $item->available_quantity }} unit</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lokasi</span>
                            <span class="font-semibold">{{ $item->location ?: '-' }}</span>
                        </div>
                    </div>

                    @if($item->status == 'tersedia' && $item->available_quantity > 0)
                        <a href="{{ route('mahasiswa.borrowings.create', $item->id) }}" class="block w-full bg-green-600 text-white text-center py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                            <i data-lucide="plus" class="w-5 h-5 inline mr-2"></i>
                            Ajukan Peminjaman
                        </a>
                    @else
                        <button disabled class="block w-full bg-gray-300 text-gray-500 text-center py-3 rounded-lg cursor-not-allowed font-semibold">
                            Barang Tidak Tersedia
                        </button>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            @if($item->description)
                <div class="mt-6">
                    <h4 class="font-semibold text-gray-900 mb-2">Deskripsi</h4>
                    <p class="text-gray-600">{{ $item->description }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection