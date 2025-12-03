@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
            <h2 class="text-2xl font-bold text-white">Edit Barang</h2>
            <p class="text-blue-100 mt-1">Update informasi barang {{ $item->name }}</p>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.items.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Kode Barang (Read Only) --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Kode Barang
                </label>
                <input 
                    type="text" 
                    value="{{ $item->item_code }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 font-mono"
                    readonly
                >
            </div>

            {{-- Nama Barang --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Nama Barang <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name"
                    value="{{ old('name', $item->name) }}"
                    placeholder="Contoh: Laptop Asus ROG"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                    required
                >
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kategori --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="category"
                    value="{{ old('category', $item->category) }}"
                    placeholder="Contoh: Elektronik"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category') border-red-500 @enderror"
                    required
                >
                @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Deskripsi
                </label>
                <textarea 
                    name="description"
                    rows="4"
                    placeholder="Deskripsi detail barang..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                >{{ old('description', $item->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jumlah & Status --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="quantity"
                        value="{{ old('quantity', $item->quantity) }}"
                        min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('quantity') border-red-500 @enderror"
                        required
                    >
                    @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="status"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror"
                        required
                    >
                        <option value="tersedia" {{ old('status', $item->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="dipinjam" {{ old('status', $item->status) == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="rusak" {{ old('status', $item->status) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="maintenance" {{ old('status', $item->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Lokasi --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Lokasi
                </label>
                <input 
                    type="text" 
                    name="location"
                    value="{{ old('location', $item->location) }}"
                    placeholder="Contoh: Ruang Lab 1"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                >
                @error('location')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Upload Foto --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Foto Barang
                </label>
                <div class="flex items-center space-x-4">
                    <label class="flex-1 flex flex-col items-center px-4 py-6 bg-white border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <i data-lucide="upload" class="w-8 h-8 text-gray-400 mb-2"></i>
                        <span class="text-sm text-gray-500">Klik untuk upload foto baru</span>
                        <span class="text-xs text-gray-400 mt-1">JPG, PNG (Max. 2MB)</span>
                        <input type="file" name="photo" accept="image/*" class="hidden" onchange="previewImage(event)">
                    </label>
                    <div id="imagePreview" class="w-32 h-32 rounded-lg overflow-hidden border-2 border-gray-300 {{ $item->photo ? '' : 'hidden' }}">
                        <img id="preview" src="{{ $item->photo ? Storage::url($item->photo) : '' }}" alt="Preview" class="w-full h-full object-cover">
                    </div>
                </div>
                @error('photo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- QR Code Section --}}
            @if($item->qr_code)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="qr-code" class="w-6 h-6 text-blue-600"></i>
                            <div>
                                <p class="font-semibold text-gray-900">QR Code tersedia</p>
                                <p class="text-sm text-gray-600">QR Code sudah digenerate untuk barang ini</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.items.download-qr', $item->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            <span>Download</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Buttons --}}
            <div class="flex space-x-3 pt-4 border-t">
                <a href="{{ route('admin.items.index') }}" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-semibold">
                    Batal
                </a>
                <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold flex items-center justify-center space-x-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    <span>Update Barang</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
@endsection