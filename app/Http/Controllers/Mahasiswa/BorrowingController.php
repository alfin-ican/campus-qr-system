<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\NotificationHelper;

class BorrowingController extends Controller
{
    // ===== DAFTAR PEMINJAMAN MAHASISWA =====
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();

        $query = Borrowing::with('item')
            ->visibleToStudent($student->id);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $borrowings = $query->latest()->paginate(10);

        $totalBorrowings = Borrowing::where('student_id', $student->id)->count();

        return view('mahasiswa.borrowings.index', compact('borrowings', 'totalBorrowings'));
    }

    // ===== FORM PENGAJUAN PEMINJAMAN =====
    public function create($item_id = null)
    {
        $item = null;
        
        if ($item_id) {
            $item = Item::findOrFail($item_id);
            
            if ($item->status != 'tersedia' || $item->available_quantity <= 0) {
                return redirect()->back()
                    ->with('error', 'Barang tidak tersedia untuk dipinjam!');
            }
        }

        // Ambil barang yang masih ada qty tersedia
        $items = Item::where('status', 'tersedia')
            ->get()
            ->filter(function($item) {
                return $item->available_quantity > 0;
            });

        return view('mahasiswa.borrowings.create', compact('item', 'items'));
    }

    // ===== PROSES PENGAJUAN PEMINJAMAN =====
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'borrow_date' => 'required|date|after_or_equal:today',
            'planned_return_date' => 'required|date|after:borrow_date',
            'purpose' => 'required|string|min:10|max:500',
        ], [
            'item_id.required' => 'Barang wajib dipilih',
            'item_id.exists' => 'Barang tidak ditemukan',
            'quantity.required' => 'Jumlah barang wajib diisi',
            'quantity.integer' => 'Jumlah harus berupa angka',
            'quantity.min' => 'Jumlah minimal 1',
            'borrow_date.required' => 'Tanggal pinjam wajib diisi',
            'borrow_date.after_or_equal' => 'Tanggal pinjam minimal hari ini',
            'planned_return_date.required' => 'Tanggal pengembalian wajib diisi',
            'planned_return_date.after' => 'Tanggal pengembalian harus setelah tanggal pinjam',
            'purpose.required' => 'Tujuan peminjaman wajib diisi',
            'purpose.min' => 'Tujuan peminjaman minimal 10 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student = Auth::guard('student')->user();
        $item = Item::findOrFail($request->item_id);

        // Cek ketersediaan qty
        if ($item->available_quantity < $request->quantity) {
            return redirect()->back()
                ->with('error', "Jumlah yang diminta ({$request->quantity}) melebihi stok tersedia ({$item->available_quantity})!")
                ->withInput();
        }

        // Cek apakah sudah ada peminjaman aktif untuk barang ini
        $existingBorrowing = Borrowing::where('student_id', $student->id)
            ->where('item_id', $item->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existingBorrowing) {
            return redirect()->back()
                ->with('error', 'Anda sudah memiliki peminjaman aktif atau pending untuk barang ini!')
                ->withInput();
        }

        // Buat peminjaman baru
        $borrowing = Borrowing::create([
            'student_id' => $student->id,
            'item_id' => $item->id,
            'quantity' => $request->quantity,
            'borrow_date' => $request->borrow_date,
            'planned_return_date' => $request->planned_return_date,
            'purpose' => $request->purpose,
            'status' => 'pending',
        ]);

        // ===== KIRIM NOTIFIKASI KE SEMUA ADMIN =====
        NotificationHelper::newBorrowingRequest($borrowing);

        return redirect()->route('mahasiswa.borrowings.show', $borrowing->id)
            ->with('success', "Pengajuan peminjaman berhasil! Kode: {$borrowing->borrowing_code}. Jumlah: {$borrowing->quantity} unit. Silakan tunggu persetujuan admin.");
    }

    // ===== DETAIL PEMINJAMAN =====
    public function show($id)
    {
        $student = Auth::guard('student')->user();

        $borrowing = Borrowing::with(['item', 'approver'])
            ->where('student_id', $student->id)
            ->findOrFail($id);

        return view('mahasiswa.borrowings.show', compact('borrowing'));
    }

    // ===== BATALKAN PEMINJAMAN =====
    public function cancel($id)
    {
        $student = Auth::guard('student')->user();

        $borrowing = Borrowing::where('student_id', $student->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $borrowing->forceDelete();

        return redirect()->route('mahasiswa.borrowings.index')
            ->with('success', 'Pengajuan peminjaman berhasil dibatalkan!');
    }

    // ===== HAPUS SATU RIWAYAT =====
    public function destroy($id)
    {
        $student = Auth::guard('student')->user();

        $borrowing = Borrowing::where('student_id', $student->id)
            ->whereIn('status', ['returned', 'rejected', 'late'])
            ->findOrFail($id);

        $borrowing->update(['hidden_by_student' => true]);

        return redirect()->route('mahasiswa.borrowings.index')
            ->with('success', 'Riwayat berhasil disembunyikan!');
    }

    // ===== HAPUS RIWAYAT TERPILIH =====
    public function destroySelected(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validator = Validator::make($request->all(), [
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:borrowings,id',
        ], [
            'selected_ids.required' => 'Pilih minimal satu data!',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $count = Borrowing::where('student_id', $student->id)
            ->whereIn('id', $request->selected_ids)
            ->whereIn('status', ['returned', 'rejected', 'late'])
            ->update(['hidden_by_student' => true]);

        if ($count == 0) {
            return redirect()->back()
                ->with('error', 'Tidak ada riwayat yang dapat disembunyikan!');
        }

        return redirect()->route('mahasiswa.borrowings.index')
            ->with('success', "{$count} riwayat berhasil disembunyikan!");
    }

    // ===== HAPUS SEMUA RIWAYAT =====
    public function destroyAllHistory(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validator = Validator::make($request->all(), [
            'confirm_text' => 'required|in:HAPUS SEMUA',
        ], [
            'confirm_text.in' => 'Ketik "HAPUS SEMUA" untuk konfirmasi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $count = Borrowing::where('student_id', $student->id)
            ->whereIn('status', ['returned', 'rejected', 'late'])
            ->update(['hidden_by_student' => true]);

        if ($count == 0) {
            return redirect()->back()
                ->with('info', 'Tidak ada riwayat untuk disembunyikan.');
        }

        return redirect()->route('mahasiswa.borrowings.index')
            ->with('success', "{$count} riwayat berhasil disembunyikan semua!");
    }
}