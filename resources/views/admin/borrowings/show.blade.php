@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Detail Peminjaman</h2>
                    <p class="text-green-100 mt-1">{{ $borrowing->borrowing_code }}</p>
                </div>
                <a href="{{ route('admin.borrowings.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition flex items-center space-x-2">
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
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-orange-100 text-orange-800">
                        Menunggu Persetujuan
                    </span>
                @elseif($borrowing->status == 'approved')
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                        Disetujui
                    </span>
                @elseif($borrowing->status == 'rejected')
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                        Ditolak
                    </span>
                @elseif($borrowing->status == 'returned')
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                        Dikembalikan
                    </span>
                @elseif($borrowing->status == 'late')
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                        Terlambat Dikembalikan
                    </span>
                @endif
            </div>

            {{-- Student Information --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Mahasiswa</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                        <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->student->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">NIM</label>
                        <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->student->student_id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->student->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">No. Telepon</label>
                        <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->student->phone ?: '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Item Information --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Barang</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Barang</label>
                        <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->item->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Barang</label>
                        <p class="px-4 py-3 bg-gray-50 rounded-lg font-mono text-gray-900">{{ $borrowing->item->item_code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                        <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->item->category }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status Barang</label>
                        <div class="px-4 py-3 bg-gray-50 rounded-lg">
                            @if($borrowing->item->status == 'tersedia')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Tersedia</span>
                            @elseif($borrowing->item->status == 'dipinjam')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Dipinjam</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Borrowing Details --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Peminjaman</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Pinjam</label>
                        <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->borrow_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Rencana Pengembalian</label>
                        <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->planned_return_date->format('d M Y') }}</p>
                    </div>
                    @if($borrowing->return_date)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Dikembalikan</label>
                            <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->return_date->format('d M Y') }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tujuan Peminjaman</label>
                        <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->purpose ?: '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Approval Information --}}
            @if($borrowing->approved_by)
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Persetujuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Disetujui Oleh</label>
                            <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->approver->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Persetujuan</label>
                            <p class="px-4 py-3 bg-gray-50 rounded-lg text-gray-900">{{ $borrowing->approved_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Rejection Reason --}}
            @if($borrowing->status == 'rejected' && $borrowing->rejection_reason)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <label class="block text-sm font-semibold text-red-700 mb-2">Alasan Penolakan</label>
                    <p class="text-red-900">{{ $borrowing->rejection_reason }}</p>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex space-x-3 pt-4 border-t">
                @if($borrowing->status == 'pending')
                    <form action="{{ route('admin.borrowings.approve', $borrowing->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold flex items-center justify-center space-x-2">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            <span>Setujui Peminjaman</span>
                        </button>
                    </form>
                    <button 
                        onclick="openRejectModal({{ $borrowing->id }})"
                        class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold flex items-center justify-center space-x-2"
                    >
                        <i data-lucide="x-circle" class="w-5 h-5"></i>
                        <span>Tolak Peminjaman</span>
                    </button>
                @elseif($borrowing->status == 'approved')
                    <form action="{{ route('admin.borrowings.return', $borrowing->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold flex items-center justify-center space-x-2">
                            <i data-lucide="package-check" class="w-5 h-5"></i>
                            <span>Tandai Dikembalikan</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
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

    // Close modal when clicking outside
    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
</script>
@endpush
@endsection