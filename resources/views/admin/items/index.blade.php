@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header Actions --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.items.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center space-x-2">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>Tambah Barang</span>
            </a>
            <button onclick="document.getElementById('filterForm').classList.toggle('hidden')" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 border flex items-center space-x-2">
                <i data-lucide="filter" class="w-5 h-5"></i>
                <span>Filter</span>
            </button>
        </div>

        {{-- Search --}}
        <form method="GET" class="w-full md:w-auto">
            <div class="relative">
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari barang..." 
                    class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                >
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></i>
            </div>
        </form>
    </div>

    {{-- Filter Form --}}
    <div id="filterForm" class="hidden bg-white rounded-xl shadow-md p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="rusak" {{ request('status') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Terapkan
                </button>
                <a href="{{ route('admin.items.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Items Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($items as $item)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                {{-- Image --}}
                <div class="h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                    @if($item->photo)
                        <img src="{{ Storage::url($item->photo) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                    @else
                        <i data-lucide="package" class="w-20 h-20 text-white opacity-50"></i>
                    @endif
                </div>

                {{-- Content --}}
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg mb-1 text-gray-900">{{ $item->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $item->category }}</p>
                            <p class="text-xs text-gray-400 font-mono mt-1">{{ $item->item_code }}</p>
                        </div>
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

                    <p class="text-sm text-gray-600 mb-4">Jumlah: <span class="font-semibold">{{ $item->quantity }}</span> unit</p>

                    {{-- Actions --}}
                    <div class="flex space-x-2">
                        @if($item->qr_code)
                            <a href="{{ route('admin.items.download-qr', $item->id) }}" class="flex-1 bg-indigo-100 text-indigo-600 py-2 rounded-lg hover:bg-indigo-200 transition flex items-center justify-center space-x-1 text-sm">
                                <i data-lucide="download" class="w-4 h-4"></i>
                                <span>QR</span>
                            </a>
                        @else
                            <form action="{{ route('admin.items.generate-qr', $item->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-indigo-100 text-indigo-600 py-2 rounded-lg hover:bg-indigo-200 transition flex items-center justify-center space-x-1 text-sm">
                                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                                    <span>Generate QR</span>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('admin.items.edit', $item->id) }}" class="flex-1 bg-blue-100 text-blue-600 py-2 rounded-lg hover:bg-blue-200 transition flex items-center justify-center space-x-1 text-sm">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                            <span>Edit</span>
                        </a>

                        <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-100 text-red-600 py-2 rounded-lg hover:bg-red-200 transition flex items-center justify-center space-x-1 text-sm">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl shadow-md p-12 text-center">
                <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4 text-gray-400"></i>
                <p class="text-gray-500 mb-4">Belum ada data barang</p>
                <a href="{{ route('admin.items.create') }}" class="inline-flex items-center space-x-2 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    <span>Tambah Barang Pertama</span>
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