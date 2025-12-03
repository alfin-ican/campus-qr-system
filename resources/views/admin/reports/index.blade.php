@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-600">Total</span>
                <i data-lucide="clipboard-list" class="w-5 h-5 text-blue-500"></i>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-600">Pending</span>
                <i data-lucide="clock" class="w-5 h-5 text-orange-500"></i>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-600">Disetujui</span>
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-600">Dikembalikan</span>
                <i data-lucide="package-check" class="w-5 h-5 text-blue-500"></i>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['returned'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-600">Terlambat</span>
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['late'] }}</p>
        </div>
    </div>

    {{-- Filter & Export --}}
    <div class="bg-white rounded-xl shadow-md p-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                    <input 
                        type="date" 
                        name="date_from" 
                        value="{{ request('date_from') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                    <input 
                        type="date" 
                        name="date_to" 
                        value="{{ request('date_to') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>

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
            </div>

            <div class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-3">
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold flex items-center justify-center space-x-2">
                    <i data-lucide="filter" class="w-5 h-5"></i>
                    <span>Terapkan Filter</span>
                </button>
                <a href="{{ route('admin.reports.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                    Reset
                </a>
                <a href="{{ route('admin.reports.export-pdf', request()->all()) }}" class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-semibold flex items-center justify-center space-x-2">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    <span>Export PDF</span>
                </a>
                <a href="{{ route('admin.reports.export-excel', request()->all()) }}" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold flex items-center justify-center space-x-2">
                    <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                    <span>Export Excel</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Report Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Mahasiswa</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Barang</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Pinjam</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Kembali</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($borrowings as $index => $borrowing)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6 text-sm text-gray-900">{{ $borrowings->firstItem() + $index }}</td>
                            <td class="py-4 px-6">
                                <span class="font-mono text-sm text-gray-900">{{ $borrowing->borrowing_code }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $borrowing->student->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $borrowing->student->student_id }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">{{ $borrowing->item->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $borrowing->item->category }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-sm text-gray-900">{{ $borrowing->borrow_date->format('d M Y') }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-sm text-gray-900">{{ $borrowing->return_date ? $borrowing->return_date->format('d M Y') : '-' }}</p>
                            </td>
                            <td class="py-4 px-6">
                                @if($borrowing->status == 'pending')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">Pending</span>
                                @elseif($borrowing->status == 'approved')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Disetujui</span>
                                @elseif($borrowing->status == 'rejected')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Ditolak</span>
                                @elseif($borrowing->status == 'returned')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Dikembalikan</span>
                                @elseif($borrowing->status == 'late')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">Terlambat</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4 text-gray-400"></i>
                                <p class="text-lg">Tidak ada data laporan</p>
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
@endsection