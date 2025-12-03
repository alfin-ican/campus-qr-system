@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Barang --}}
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Barang</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_items'] }}</p>
                </div>
                <div class="bg-blue-500 p-3 rounded-lg">
                    <i data-lucide="package" class="w-8 h-8 text-white"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.items.index') }}" class="text-sm text-blue-600 hover:text-blue-700 flex items-center">
                    Lihat Detail
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Unit Dipinjam --}}
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Unit Dipinjam</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['borrowed_items'] }}</p>
                </div>
                <div class="bg-yellow-500 p-3 rounded-lg">
                    <i data-lucide="clipboard-list" class="w-8 h-8 text-white"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.borrowings.index') }}?status=approved" class="text-sm text-yellow-600 hover:text-yellow-700 flex items-center">
                    Lihat Detail
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Pengajuan Pending --}}
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Pengajuan Pending</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['pending_borrowings'] }}</p>
                </div>
                <div class="bg-orange-500 p-3 rounded-lg">
                    <i data-lucide="clock" class="w-8 h-8 text-white"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.borrowings.index') }}?status=pending" class="text-sm text-orange-600 hover:text-orange-700 flex items-center">
                    Lihat Detail
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Total Mahasiswa --}}
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Mahasiswa</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_students'] }}</p>
                </div>
                <div class="bg-green-500 p-3 rounded-lg">
                    <i data-lucide="users" class="w-8 h-8 text-white"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.users.students.index') }}" class="text-sm text-green-600 hover:text-green-700 flex items-center">
                    Lihat Detail
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Borrowings Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">Pengajuan Peminjaman Terbaru</h2>
                <a href="{{ route('admin.borrowings.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 flex items-center">
                    Lihat Semua
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Mahasiswa</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Barang</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentBorrowings as $borrowing)
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
                                <p class="text-sm text-gray-500">{{ $borrowing->item->item_code }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-sm text-gray-900">{{ $borrowing->borrow_date->format('d M Y') }}</p>
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
                                        <button onclick="openRejectModal({{ $borrowing->id }})" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="Tolak">
                                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.borrowings.show', $borrowing->id) }}" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition" title="Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-gray-400"></i>
                                <p>Belum ada pengajuan peminjaman</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Chart 1: Statistik Peminjaman Bulanan --}}
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Statistik Peminjaman Bulanan ({{ now()->year }})</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        {{-- Chart 2: Barang Paling Sering Dipinjam --}}
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Barang Paling Sering Dipinjam (Top 5)</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="topItemsChart"></canvas>
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
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Reject Modal Functions
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

    // ===== CHART 1: Statistik Peminjaman Bulanan =====
    const monthlyData = @json($monthlyData);
    const monthlyLabels = monthlyData.map(d => d.month);
    const monthlyValues = monthlyData.map(d => d.total);

    const monthlyChart = new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: monthlyValues,
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // ===== CHART 2: Barang Paling Sering Dipinjam =====
    const topItems = @json($topItems);
    const itemLabels = topItems.map(item => item.item.name);
    const itemCounts = topItems.map(item => item.borrow_count);

    const topItemsChart = new Chart(document.getElementById('topItemsChart'), {
        type: 'bar',
        data: {
            labels: itemLabels,
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: itemCounts,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ],
                borderColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(251, 191, 36)',
                    'rgb(239, 68, 68)',
                    'rgb(139, 92, 246)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection