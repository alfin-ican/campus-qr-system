<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\NotificationHelper;

class BorrowingController extends Controller
{
    // ===== TAMPILKAN DAFTAR PEMINJAMAN =====
    public function index(Request $request)
    {
        $query = Borrowing::with(['student', 'item', 'approver'])
            ->visibleToAdmin();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('borrow_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('borrow_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('borrowing_code', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('item', function($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $borrowings = $query->latest()->paginate(8);

        return view('admin.borrowings.index', compact('borrowings'));
    }

    // ===== DETAIL PEMINJAMAN =====
    public function show($id)
    {
        $borrowing = Borrowing::with(['student', 'item', 'approver'])
            ->findOrFail($id);

        return view('admin.borrowings.show', compact('borrowing'));
    }

    // ===== APPROVE PEMINJAMAN =====
    public function approve($id)
    {
        $borrowing = Borrowing::with(['student', 'item'])->findOrFail($id);

        if ($borrowing->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Peminjaman ini sudah diproses sebelumnya!');
        }

        $item = $borrowing->item;
        
        // Cek apakah qty yang diminta tersedia
        if ($item->available_quantity < $borrowing->quantity) {
            return redirect()->back()
                ->with('error', "Barang tidak tersedia! Tersedia: {$item->available_quantity}, Diminta: {$borrowing->quantity}");
        }

        $borrowing->update([
            'status' => 'approved',
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now(),
        ]);

        // Update status barang jika semua qty dipinjam
        if ($item->available_quantity == 0) {
            $item->update(['status' => 'dipinjam']);
        }

        // ===== KIRIM NOTIFIKASI KE MAHASISWA =====
        NotificationHelper::borrowingApproved($borrowing);

        return redirect()->back()
            ->with('success', "Peminjaman berhasil disetujui! ({$borrowing->quantity} unit)");
    }

    // ===== REJECT PEMINJAMAN =====
    public function reject(Request $request, $id)
    {
        $borrowing = Borrowing::with(['student', 'item'])->findOrFail($id);

        if ($borrowing->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Peminjaman ini sudah diproses sebelumnya!');
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|min:10',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $borrowing->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now(),
        ]);

        // ===== KIRIM NOTIFIKASI KE MAHASISWA =====
        NotificationHelper::borrowingRejected($borrowing, $request->rejection_reason);

        return redirect()->back()
            ->with('success', 'Peminjaman berhasil ditolak!');
    }

    // ===== PROSES PENGEMBALIAN BARANG =====
    public function returnItem($id)
    {
        $borrowing = Borrowing::with(['student', 'item'])->findOrFail($id);

        if ($borrowing->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Barang ini belum dipinjam atau sudah dikembalikan!');
        }

        // Tentukan status: returned atau late
        if (now()->gt($borrowing->planned_return_date)) {
            $status = 'late';
            $message = "Barang berhasil dikembalikan! (Terlambat - {$borrowing->quantity} unit)";
        } else {
            $status = 'returned';
            $message = "Barang berhasil dikembalikan tepat waktu! ({$borrowing->quantity} unit)";
        }

        $borrowing->update([
            'status' => $status,
            'return_date' => now(),
        ]);

        // Update status barang jika ada qty yang tersedia
        $item = $borrowing->item;
        if ($item->available_quantity > 0 && $item->status == 'dipinjam') {
            $item->update(['status' => 'tersedia']);
        }

        // ===== KIRIM NOTIFIKASI KE SEMUA ADMIN =====
        NotificationHelper::itemReturned($borrowing);

        return redirect()->back()->with('success', $message);
    }

    // ===== HAPUS SATU RIWAYAT =====
    public function destroy($id)
    {
        $borrowing = Borrowing::findOrFail($id);

        if (in_array($borrowing->status, ['pending', 'approved'])) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus peminjaman yang masih aktif!');
        }

        $borrowing->update(['hidden_by_admin' => true]);

        return redirect()->back()
            ->with('success', 'Riwayat peminjaman berhasil disembunyikan!');
    }

    // ===== HAPUS RIWAYAT TERPILIH =====
    public function destroySelected(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:borrowings,id',
        ], [
            'selected_ids.required' => 'Pilih minimal satu peminjaman untuk disembunyikan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $borrowings = Borrowing::whereIn('id', $request->selected_ids)->get();

        $active = $borrowings->whereIn('status', ['pending', 'approved']);

        if ($active->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menyembunyikan peminjaman yang masih aktif!');
        }

        Borrowing::whereIn('id', $request->selected_ids)
            ->update(['hidden_by_admin' => true]);

        return redirect()->back()
            ->with('success', 'Riwayat terpilih berhasil disembunyikan!');
    }

    // ===== HAPUS SEMUA RIWAYAT =====
    public function destroyAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filter_status' => 'nullable|in:returned,rejected,late',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $query = Borrowing::visibleToAdmin(); // ✅ Hanya yang belum di-hide

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        } else {
            $query->whereIn('status', ['returned', 'rejected', 'late']);
        }

        $count = $query->count();

        if ($count == 0) {
            return redirect()->back()
                ->with('info', 'Tidak ada riwayat yang dapat disembunyikan.');
        }

        $query->update(['hidden_by_admin' => true]);

        return redirect()->back()
            ->with('success', "Berhasil menyembunyikan {$count} riwayat peminjaman!");
    }

    // ===== TAMPILKAN RIWAYAT TERSEMBUNYI (TRASH) =====
    public function trash(Request $request)
    {
        $query = Borrowing::with(['student', 'item', 'approver'])
            ->where('hidden_by_admin', true);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $trashed = $query->latest()->paginate(10);

        return view('admin.borrowings.trash', compact('trashed'));
    }

    // ===== PULIHKAN SATU RIWAYAT =====
    public function restore($id)
    {
        $borrowing = Borrowing::where('hidden_by_admin', true)
            ->findOrFail($id);

        $borrowing->update(['hidden_by_admin' => false]);

        return redirect()->back()
            ->with('success', 'Riwayat berhasil dikembalikan!');
    }

    // ===== PULIHKAN SEMUA RIWAYAT =====
    public function restoreAll()
    {
        $count = Borrowing::where('hidden_by_admin', true)->count();

        if ($count == 0) {
            return redirect()->back()
                ->with('info', 'Tidak ada riwayat untuk dipulihkan.');
        }

        Borrowing::where('hidden_by_admin', true)
            ->update(['hidden_by_admin' => false]);

        return redirect()->back()
            ->with('success', "{$count} riwayat berhasil dipulihkan!");
    }
}