@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ selectedIds: [], selectAll: false }">
    {{-- Header Actions --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex items-center space-x-3">
            <button onclick="document.getElementById('filterForm').classList.toggle('hidden')" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 border flex items-center space-x-2">
                <i data-lucide="filter" class="w-5 h-5"></i>
                <span>Filter</span>
            </button>

            {{-- Tombol Hapus Terpilih --}}
            <button 
                @click="if(selectedIds.length > 0 && confirm('Yakin ingin menghapus ' + selectedIds.length + ' riwayat peminjaman?')) { document.getElementById('deleteSelectedForm').submit(); }"
                x-show="selectedIds.length > 0"
                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center space-x-2"
                x-cloak
            >
                <i data-lucide="trash-2" class="w-5 h-5"></i>
                <span>Hapus Terpilih (<span x-text="selectedIds.length"></span>)</span>
            </button>

            {{-- Tombol Hapus Semua Riwayat --}}
            <button 
                onclick="openDeleteAllModal()"
                class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 flex items-center space-x-2"
            >
                <i data-lucide="trash" class="w-5 h-5"></i>
                <span>Hapus Semua Riwayat</span>
            </button>

            {{-- Tombol Lihat Trash (Opsional) --}}
            <a href="{{ route('admin.borrowings.trash') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center space-x-2">
                <i data-lucide="archive" class="w-5 h-5"></i>
                <span>Trash</span>
            </a>
        </div>

        {{-- Search --}}
        <form method="GET" class="w-full md:w-auto">
            <div class="relative">
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari peminjaman..." 
                    class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                >
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></i>
            </div>
        </form>
    </div>

    {{-- Filter Form --}}
    <div id="filterForm" class="hidden bg-white rounded-xl shadow-md p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Terapkan
                </button>
                <a href="{{ route('admin.borrowings.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Form untuk Delete Selected (Hidden) --}}
    <form id="deleteSelectedForm" action="{{ route('admin.borrowings.destroy-selected') }}" method="POST" style="display: none;">
        @csrf
        <template x-for="id in selectedIds">
            <input type="hidden" name="selected_ids[]" :value="id">
        </template>
    </form>

    {{-- Borrowings Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-6">
                            <input 
                                type="checkbox" 
                                @change="selectAll = !selectAll; selectedIds = selectAll ? {{ $borrowings->pluck('id')->toJson() }} : []"
                                :checked="selectAll"
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            >
                        </th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Mahasiswa</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Barang</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Pinjam</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Rencana Kembali</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($borrowings as $borrowing)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <input 
                                    type="checkbox" 
                                    :value="{{ $borrowing->id }}"
                                    x-model="selectedIds"
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                >
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-mono text-sm text-gray-900">{{ $borrowing->borrowing_code }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $borrowing->student->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $borrowing->student->student_id }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $borrowing->item->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $borrowing->item->item_code }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-sm text-gray-900">{{ $borrowing->borrow_date->format('d M Y') }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-sm text-gray-900">{{ $borrowing->planned_return_date->format('d M Y') }}</p>
                            </td>
                            <td class="py-4 px-6">
                                @if($borrowing->status == 'pending')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">Pending</span>
                                @elseif($borrowing->status == 'approved')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Disetujui</span>
                                @elseif($borrowing->status == 'rejected')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Ditolak</span>
                                @elseif($borrowing->status == 'returned')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Dikembalikan</span>
                                @elseif($borrowing->status == 'late')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">Terlambat</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex space-x-2">
                                    @if($borrowing->status == 'pending')
                                        <form action="{{ route('admin.borrowings.approve', $borrowing->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition" title="Setujui">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <button 
                                            onclick="openRejectModal({{ $borrowing->id }})"
                                            class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" 
                                            title="Tolak"
                                        >
                                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                                        </button>
                                    @elseif($borrowing->status == 'approved')
                                        <form action="{{ route('admin.borrowings.return', $borrowing->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition" title="Kembalikan">
                                                <i data-lucide="package-check" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.borrowings.show', $borrowing->id) }}" class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition" title="Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>

                                    {{-- Tombol Hapus (hanya untuk status selesai) --}}
                                    @if(in_array($borrowing->status, ['returned', 'rejected', 'late']))
                                        <form action="{{ route('admin.borrowings.destroy', $borrowing->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus riwayat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="Hapus Riwayat">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500">
                                <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4 text-gray-400"></i>
                                <p class="text-lg">Tidak ada data peminjaman</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($borrowings->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $borrowings->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Tolak Peminjaman</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="rejection_reason"
                    rows="4"
                    placeholder="Masukkan alasan penolakan (minimal 10 karakter)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    required
                ></textarea>
            </div>
            <div class="flex space-x-3">
                <button 
                    type="button"
                    onclick="closeRejectModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                >
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete All Modal --}}
<div id="deleteAllModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Hapus Semua Riwayat Peminjaman</h3>
        <form action="{{ route('admin.borrowings.destroy-all') }}" method="POST">
            @csrf
            <div class="mb-4">
                <p class="text-gray-700 mb-4">Pilih riwayat yang ingin dihapus:</p>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="filter_status" value="" class="mr-2" checked>
                        <span>Semua riwayat selesai (Dikembalikan, Ditolak, Terlambat)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="filter_status" value="returned" class="mr-2">
                        <span>Hanya yang dikembalikan</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="filter_status" value="rejected" class="mr-2">
                        <span>Hanya yang ditolak</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="filter_status" value="late" class="mr-2">
                        <span>Hanya yang terlambat</span>
                    </label>
                </div>
                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-sm text-yellow-800 flex items-start">
                        <i data-lucide="alert-triangle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span>Peminjaman yang masih aktif (Pending/Disetujui) tidak akan dihapus</span>
                    </p>
                </div>
            </div>
            <div class="flex space-x-3">
                <button 
                    type="button"
                    onclick="closeDeleteAllModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    onclick="return confirm('Yakin ingin menghapus semua riwayat yang dipilih?')"
                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                >
                    Hapus Semua
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(borrowingId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = `/admin/borrowings/${borrowingId}/reject`;
        modal.classList.remove('hidden');
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
    }

    function openDeleteAllModal() {
        const modal = document.getElementById('deleteAllModal');
        modal.classList.add('hidden');
        setTimeout(() => modal.classList.remove('hidden'), 100);
    }

    function closeDeleteAllModal() {
        const modal = document.getElementById('deleteAllModal');
        modal.classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });

    document.getElementById('deleteAllModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteAllModal();
        }
    });
</script>
@endpush
@endsection