@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header Actions --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex items-center space-x-3">
            <button onclick="window.location.href='{{ route('admin.users.students.index') }}'" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 border flex items-center space-x-2">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span>Lihat Mahasiswa</span>
            </button>
        </div>

        {{-- Search --}}
        <form method="GET" class="w-full md:w-auto">
            <div class="relative">
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari admin/petugas..." 
                    class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                >
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></i>
            </div>
        </form>
    </div>

    {{-- Admins Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">ID Admin</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Last Login</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($admins as $admin)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <span class="font-mono text-sm text-gray-900">{{ $admin->admin_id }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        @if($admin->photo)
                                            <img src="{{ Storage::url($admin->photo) }}" alt="{{ $admin->name }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <i data-lucide="user" class="w-5 h-5 text-indigo-600"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $admin->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-sm text-gray-900">{{ $admin->email }}</p>
                            </td>
                            <td class="py-4 px-6">
                                @if($admin->role == 'admin')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                        Admin
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        Petugas
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if($admin->is_active)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-sm text-gray-900">{{ $admin->last_login ? $admin->last_login->diffForHumans() : 'Belum pernah' }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.users.admins.show', $admin->id) }}" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition" title="Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    @if($admin->id != Auth::guard('admin')->id())
                                        <form action="{{ route('admin.users.admins.toggle-status', $admin->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 bg-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-200 transition" title="Toggle Status">
                                                <i data-lucide="power" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.admins.destroy', $admin->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus admin ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4 text-gray-400"></i>
                                <p class="text-lg">Tidak ada data admin/petugas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($admins->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $admins->links() }}
            </div>
        @endif
    </div>
</div>
@endsection