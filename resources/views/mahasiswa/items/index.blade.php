@extends('layouts.mahasiswa')

@section('content')
<div class="space-y-6">
    {{-- Search & Filter --}}
    <div class="bg-white rounded-xl shadow-md p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama barang atau kode..." 
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                    <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-3.5"></i>
                </div>
            </div>

            <div>
                <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                    Cari
                </button>
                <a href="{{ route('mahasiswa.items.index') }}" class="px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Items Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($items as $item)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                {{-- Image --}}
                <div class="h-40 bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                    @if($item->photo)
                        <img src="{{ Storage::url($item->photo) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                    @else
                        <i data-lucide="package" class="w-16 h-16 text-white opacity-50"></i>
                    @endif
                </div>

                {{-- Content --}}
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900">{{ $item->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $item->category }}</p>
                        </div>
                        @if($item->status == 'tersedia')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                Tersedia
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                {{ ucfirst($item->status) }}
                            </span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 mb-3">
                        <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                        {{ $item->location ?: 'Lokasi tidak tersedia' }}
                    </p>

                    <div class="flex space-x-2">
                        <a href="{{ route('mahasiswa.items.show', $item->id) }}" class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 transition text-center text-sm">
                            Detail
                        </a>
                        @if($item->status == 'tersedia' && $item->available_quantity > 0)
                            <a href="{{ route('mahasiswa.borrowings.create', $item->id) }}" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition text-center text-sm">
                                Pinjam
                            </a>
                        @else
                            <button disabled class="flex-1 bg-gray-300 text-gray-500 py-2 rounded-lg cursor-not-allowed text-sm">
                                Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl shadow-md p-12 text-center">
                <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4 text-gray-400"></i>
                <p class="text-gray-500 text-lg">Tidak ada barang ditemukan</p>
                <a href="{{ route('mahasiswa.items.index') }}" class="mt-4 inline-block text-green-600 hover:text-green-700">
                    Reset pencarian
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($items->hasPages())
        <div class="bg-white rounded-xl shadow-md p-4">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection