@extends('layouts.mahasiswa')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Ajukan Peminjaman Barang</h2>
            <p class="text-gray-600 mt-1">Lengkapi form di bawah untuk mengajukan peminjaman</p>
        </div>

        <form action="{{ route('mahasiswa.borrowings.store') }}" method="POST">
            @csrf

            {{-- Pilih Barang --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Pilih Barang <span class="text-red-500">*</span>
                </label>
                <select name="item_id" id="item_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('item_id') border-red-500 @enderror"
                    required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($items as $itemOption)
                        <option 
                            value="{{ $itemOption->id }}" 
                            data-available="{{ $itemOption->available_quantity }}"
                            data-total="{{ $itemOption->quantity }}"
                            {{ old('item_id', $item->id ?? '') == $itemOption->id ? 'selected' : '' }}>
                            {{ $itemOption->name }} - Tersedia: {{ $itemOption->available_quantity }}/{{ $itemOption->quantity }}
                        </option>
                    @endforeach
                </select>
                @error('item_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jumlah Barang --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Jumlah Barang <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    name="quantity" 
                    id="quantity"
                    value="{{ old('quantity', 1) }}"
                    min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('quantity') border-red-500 @enderror"
                    required>
                <p class="text-sm text-gray-500 mt-1" id="qty-info">
                    Masukkan jumlah barang yang ingin dipinjam
                </p>
                @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Pinjam --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tanggal Pinjam <span class="text-red-500">*</span>
                </label>
                <input 
                    type="date" 
                    name="borrow_date" 
                    value="{{ old('borrow_date', date('Y-m-d')) }}"
                    min="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('borrow_date') border-red-500 @enderror"
                    required>
                @error('borrow_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Rencana Kembali --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Rencana Tanggal Kembali <span class="text-red-500">*</span>
                </label>
                <input 
                    type="date" 
                    name="planned_return_date" 
                    value="{{ old('planned_return_date') }}"
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('planned_return_date') border-red-500 @enderror"
                    required>
                @error('planned_return_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tujuan Peminjaman --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tujuan Peminjaman <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="purpose" 
                    rows="4"
                    placeholder="Jelaskan tujuan peminjaman barang ini..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('purpose') border-red-500 @enderror"
                    required>{{ old('purpose') }}</textarea>
                @error('purpose')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('mahasiswa.borrowings.index') }}" 
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </a>
                <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Update max quantity saat barang dipilih
    document.getElementById('item_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const available = selectedOption.getAttribute('data-available');
        const total = selectedOption.getAttribute('data-total');
        const qtyInput = document.getElementById('quantity');
        const qtyInfo = document.getElementById('qty-info');
        
        if (available) {
            qtyInput.max = available;
            qtyInput.value = Math.min(qtyInput.value, available);
            qtyInfo.textContent = `Tersedia: ${available} dari ${total} unit`;
            qtyInfo.classList.remove('text-gray-500');
            qtyInfo.classList.add('text-indigo-600');
        }
    });

    // Validasi quantity tidak melebihi max
    document.getElementById('quantity').addEventListener('input', function() {
        const max = parseInt(this.max);
        if (max && parseInt(this.value) > max) {
            this.value = max;
        }
        if (parseInt(this.value) < 1) {
            this.value = 1;
        }
    });

    // Trigger change event jika ada item pre-selected
    @if(isset($item))
        document.getElementById('item_id').dispatchEvent(new Event('change'));
    @endif
</script>
@endpush
@endsection