@extends('layouts.mahasiswa')

@section('content')
<div class="space-y-6" x-data="{ selectedIds: [], selectAll: false, showDeleteAllModal: false }">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Peminjaman</h2>
            <p class="text-gray-600 mt-1">Total {{ $totalBorrowings }} peminjaman</p>
        </div>
        <a href="{{ route('mahasiswa.borrowings.create') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 flex items-center space-x-2">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Ajukan Peminjaman</span>
        </a>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-3">
        {{-- Tombol Hapus Terpilih --}}
        <button 
            @click="if(selectedIds.length > 0 && confirm('Yakin ingin menghapus ' + selectedIds.length + ' riwayat?')) { document.getElementById('deleteSelectedForm').submit(); }"
            x-show="selectedIds.length > 0"
            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center space-x-2"
            x-cloak
        >
            <i data-lucide="trash-2" class="w-5 h-5"></i>
            <span>Hapus Terpilih (<span x-text="selectedIds.length"></span>)</span>
        </button>

        {{-- Tombol Hapus Semua Riwayat --}}
        <button 
            @click="showDeleteAllModal = true"
            class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 flex items-center space-x-2"
        >
            <i data-lucide="trash" class="w-5 h-5"></i>
            <span>Hapus Semua Riwayat</span>
        </button>
    </div>

    {{-- Form Hidden untuk Delete Selected --}}
    <form id="deleteSelectedForm" action="{{ route('mahasiswa.borrowings.destroy-selected') }}" method="POST" style="display: none;">
        @csrf
        <template x-for="id in selectedIds">
            <input type="hidden" name="selected_ids[]" :value="id">
        </template>
    </form>

    {{-- Filter Status --}}
    <div class="bg-white rounded-xl shadow-md p-4">
        <form method="GET" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <div class="flex-1">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                Filter
            </button>
            <a href="{{ route('mahasiswa.borrowings.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 text-center">
                Reset
            </a>
        </form>
    </div>

    {{-- Borrowings List --}}
    <div class="space-y-4">
        @forelse($borrowings as $borrowing)
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition">
                <div class="p-6">
                    <div class="flex items-start space-x-4">
                        {{-- Checkbox (hanya untuk status selesai) --}}
                        @if(in_array($borrowing->status, ['returned', 'rejected', 'late']))
                            <input 
                                type="checkbox" 
                                :value="{{ $borrowing->id }}"
                                x-model="selectedIds"
                                class="mt-1 w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                            >
                        @else
                            <div class="w-5 h-5"></div>
                        @endif

                        <div class="flex-1">
                            {{-- Header: Nama Barang & Status --}}
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-3 gap-2">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $borrowing->item->name }}</h3>
                                    <p class="text-sm text-gray-500 font-mono">{{ $borrowing->borrowing_code }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $borrowing->item->category }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($borrowing->status == 'pending')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 whitespace-nowrap">
                                            <i data-lucide="clock" class="w-3 h-3 inline"></i> Pending
                                        </span>
                                    @elseif($borrowing->status == 'approved')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 whitespace-nowrap">
                                            <i data-lucide="check-circle" class="w-3 h-3 inline"></i> Disetujui
                                        </span>
                                    @elseif($borrowing->status == 'rejected')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 whitespace-nowrap">
                                            <i data-lucide="x-circle" class="w-3 h-3 inline"></i> Ditolak
                                        </span>
                                    @elseif($borrowing->status == 'returned')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 whitespace-nowrap">
                                            <i data-lucide="package-check" class="w-3 h-3 inline"></i> Dikembalikan
                                        </span>
                                    @elseif($borrowing->status == 'late')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 whitespace-nowrap">
                                            <i data-lucide="alert-triangle" class="w-3 h-3 inline"></i> Terlambat
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Detail Info --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-4">
                                <div>
                                    <p class="text-gray-500 flex items-center">
                                        <i data-lucide="calendar" class="w-4 h-4 mr-1"></i>
                                        Tanggal Pinjam
                                    </p>
                                    <p class="font-semibold text-gray-900">{{ $borrowing->borrow_date->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 flex items-center">
                                        <i data-lucide="calendar-check" class="w-4 h-4 mr-1"></i>
                                        Rencana Kembali
                                    </p>
                                    <p class="font-semibold text-gray-900">{{ $borrowing->planned_return_date->format('d M Y') }}</p>
                                </div>
                                @if($borrowing->return_date)
                                    <div>
                                        <p class="text-gray-500 flex items-center">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i>
                                            Tanggal Kembali
                                        </p>
                                        <p class="font-semibold text-gray-900">{{ $borrowing->return_date->format('d M Y') }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Tujuan Peminjaman --}}
                            @if($borrowing->purpose)
                                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                    <p class="text-xs text-gray-500 mb-1">Tujuan Peminjaman:</p>
                                    <p class="text-sm text-gray-700">{{ $borrowing->purpose }}</p>
                                </div>
                            @endif

                            {{-- Alasan Penolakan --}}
                            @if($borrowing->rejection_reason)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                                    <p class="text-xs text-red-600 font-semibold mb-1 flex items-center">
                                        <i data-lucide="info" class="w-4 h-4 mr-1"></i>
                                        Alasan Ditolak:
                                    </p>
                                    <p class="text-sm text-red-800">{{ $borrowing->rejection_reason }}</p>
                                </div>
                            @endif

                            {{-- Approved By --}}
                            @if($borrowing->approver)
                                <div class="text-xs text-gray-500 mb-3">
                                    <i data-lucide="user-check" class="w-3 h-3 inline"></i>
                                    Diproses oleh: <span class="font-semibold">{{ $borrowing->approver->name }}</span>
                                    @if($borrowing->approved_at)
                                        pada {{ $borrowing->approved_at->format('d M Y H:i') }}
                                    @endif
                                </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('mahasiswa.borrowings.show', $borrowing->id) }}" class="px-4 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition flex items-center space-x-2">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    <span>Detail</span>
                                </a>

                                @if($borrowing->status == 'pending')
                                    <form action="{{ route('mahasiswa.borrowings.cancel', $borrowing->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-orange-100 text-orange-600 rounded-lg hover:bg-orange-200 transition flex items-center space-x-2">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                            <span>Batalkan</span>
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($borrowing->status, ['returned', 'rejected', 'late']))
                                    <form action="{{ route('mahasiswa.borrowings.destroy', $borrowing->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus riwayat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition flex items-center space-x-2">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4 text-gray-400"></i>
                <p class="text-gray-500 text-lg mb-4">Belum ada riwayat peminjaman</p>
                <a href="{{ route('mahasiswa.borrowings.create') }}" class="inline-flex items-center space-x-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    <span>Ajukan Peminjaman Pertama</span>
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($borrowings->hasPages())
        <div class="bg-white rounded-xl shadow-md p-4">
            {{ $borrowings->appends(request()->query())->links() }}
        </div>
    @endif
</div>

{{-- Modal Hapus Semua --}}
<div 
    x-show="showDeleteAllModal" 
    x-cloak
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
    @click.self="showDeleteAllModal = false"
>
    <div class="bg-white rounded-xl max-w-md w-full p-6" @click.stop>
        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 mr-2"></i>
            Hapus Semua Riwayat
        </h3>
        
        <div class="mb-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-red-800 mb-2">
                    <strong>Peringatan:</strong> Tindakan ini akan menghapus semua riwayat peminjaman yang sudah selesai (Dikembalikan, Ditolak, Terlambat).
                </p>
                <p class="text-sm text-red-800">
                    Data yang sudah dihapus <strong>tidak dapat dipulihkan</strong>!
                </p>
            </div>

            <form action="{{ route('mahasiswa.borrowings.destroy-all-history') }}" method="POST" id="deleteAllForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Ketik <span class="text-red-600 font-bold">HAPUS SEMUA</span> untuk konfirmasi:
                    </label>
                    <input 
                        type="text" 
                        name="confirm_text"
                        placeholder="HAPUS SEMUA"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 uppercase"
                        required
                    >
                </div>

                <div class="flex space-x-3">
                    <button 
                        type="button"
                        @click="showDeleteAllModal = false"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center space-x-2"
                    >
                        <i data-lucide="trash" class="w-4 h-4"></i>
                        <span>Hapus Semua</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
    
    // Reinitialize icons after Alpine updates
    document.addEventListener('alpine:initialized', () => {
        lucide.createIcons();
    });
</script>
@endpush
@endsection