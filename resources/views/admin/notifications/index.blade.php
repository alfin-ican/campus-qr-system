@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Semua Notifikasi</h2>
            <p class="text-gray-600 mt-1">Kelola notifikasi Anda</p>
        </div>
        
        @if($notifications->where('is_read', false)->count() > 0)
        <button 
            onclick="markAllAsRead()"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
        >
            <i data-lucide="check-double" class="w-4 h-4 inline mr-2"></i>
            Tandai Semua Dibaca
        </button>
        @endif
    </div>

    {{-- Notifications List --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @forelse($notifications as $notification)
            <div class="p-4 border-b border-gray-200 last:border-0 hover:bg-gray-50 transition {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                <div class="flex items-start space-x-4">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center
                        {{ $notification->type === 'peminjaman_baru' ? 'bg-blue-100 text-blue-600' : '' }}
                        {{ $notification->type === 'approve' ? 'bg-green-100 text-green-600' : '' }}
                        {{ $notification->type === 'tolak' ? 'bg-red-100 text-red-600' : '' }}
                        {{ $notification->type === 'peringatan_pengembalian' ? 'bg-yellow-100 text-yellow-600' : '' }}
                        {{ !$notification->type ? 'bg-gray-100 text-gray-600' : '' }}
                    ">
                        <i data-lucide="{{ $notification->icon }}" class="w-6 h-6"></i>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $notification->title }}</h3>
                                <p class="text-gray-600 mt-1">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-500 mt-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center space-x-2">
                                @if(!$notification->is_read)
                                    <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                @endif

                                @if($notification->url)
                                    <a href="{{ $notification->url }}" 
                                       class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                       title="Lihat Detail">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                @endif

                                <button 
                                    onclick="deleteNotification({{ $notification->id }})"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                    title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-gray-500">
                <i data-lucide="bell-off" class="w-16 h-16 mx-auto mb-4 text-gray-400"></i>
                <p class="text-lg font-medium">Tidak ada notifikasi</p>
                <p class="text-sm mt-1">Anda akan menerima notifikasi di sini</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    async function markAllAsRead() {
        if (!confirm('Tandai semua notifikasi sebagai sudah dibaca?')) return;

        try {
            const response = await fetch('{{ route('admin.notifications.mark-all-read') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                location.reload();
            } else {
                alert('Gagal menandai notifikasi');
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan');
        }
    }

    async function deleteNotification(id) {
        if (!confirm('Hapus notifikasi ini?')) return;

        try {
            const response = await fetch(`/admin/notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                location.reload();
            } else {
                alert('Gagal menghapus notifikasi');
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan');
        }
    }
</script>
@endpush
@endsection
