@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header Actions --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex items-center space-x-3">
            <button onclick="window.location.href='{{ route('admin.users.admins.index') }}'" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 border flex items-center space-x-2">
                <i data-lucide="shield" class="w-5 h-5"></i>
                <span>Lihat Admin/Petugas</span>
            </button>
        </div>

        {{-- Search --}}
        <form method="GET" class="w-full md:w-auto">
            <div class="relative">
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari mahasiswa..." 
                    class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                >
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></i>
            </div>
        </form>
    </div>

    {{-- Students Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">NIM</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Jurusan</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Peminjaman</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <span class="font-mono text-sm text-gray-900">{{ $student->student_id }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                        @if($student->photo)
                                            <img src="{{ Storage::url($student->photo) }}" alt="{{ $student->name }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <i data-lucide="user" class="w-5 h-5 text-green-600"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $student->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-sm text-gray-900">{{ $student->email }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-sm text-gray-900">{{ $student->major ?: '-' }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                    {{ $student->borrowings_count }} kali
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($student->is_active)
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
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.users.students.show', $student->id) }}" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition" title="Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.users.students.toggle-status', $student->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 bg-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-200 transition" title="Toggle Status">
                                            <i data-lucide="power" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus mahasiswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4 text-gray-400"></i>
                                <p class="text-lg">Tidak ada data mahasiswa</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection