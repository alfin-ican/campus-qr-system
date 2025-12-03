@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Trash - Riwayat Terhapus</h2>
            <p class="text-gray-600 mt-1">Kelola riwayat peminjaman yang telah dihapus</p>
        </div>
        <a href="{{ route('admin.borrowings.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center space-x-2">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            <span>Kembali</span>
        </a>
    </div>

    {{-- Action Buttons --}}
    @if($trashed->count() > 0)
    <div class="flex justify-end space-x-3">
        <form action="{{ route('admin.borrowings.restore-all') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center space-x-2">
                <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                <span>Pulihkan Semua</span>
            </button>
        </form>
    </div>
    @endif

    {{-- Trash Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Mahasiswa</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Barang</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($trashed as $borrowing)
                        <tr class="hover:bg-gray-50 transition">
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
                                <p class="font-medium text-gray-900">{{ $borrowing->item->name }}</p>
                            </td>

                            <td class="py-4 px-6">
                                @if($borrowing->status == 'returned')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Dikembalikan</span>
                                @elseif($borrowing->status == 'rejected')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Ditolak</span>
                                @elseif($borrowing->status == 'late')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">Terlambat</span>
                                @endif
                            </td>

                            <td class="py-4 px-6">
                                <p class="text-sm text-gray-900">{{ $borrowing->borrow_date->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $borrowing->return_date ? $borrowing->return_date->format('d M Y') : '-' }}</p>
                            </td>

                            <td class="py-4 px-6">
                                <form action="{{ route('admin.borrowings.restore', $borrowing->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition" title="Pulihkan">
                                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4 text-gray-400"></i>
                                <p class="text-lg">Tidak ada riwayat di trash</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($trashed->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $trashed->links() }}
            </div>
        @endif
    </div>
</div>
@endsection