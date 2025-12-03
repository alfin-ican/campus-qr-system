<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();

        // ===== STATISTIK PEMINJAMAN MAHASISWA =====
        $stats = [
            // Total semua peminjaman (termasuk yang di-hide)
            'total_borrowed' => Borrowing::where('student_id', $student->id)->count(),
            
            // Pending (tidak termasuk yang di-hide)
            'pending' => Borrowing::where('student_id', $student->id)
                ->where('status', 'pending')
                ->where('hidden_by_student', false)
                ->count(),
            
            // Approved/Sedang Dipinjam (tidak termasuk yang di-hide)
            'approved' => Borrowing::where('student_id', $student->id)
                ->where('status', 'approved')
                ->where('hidden_by_student', false)
                ->count(),
            
            // Returned (tidak termasuk yang di-hide)
            'returned' => Borrowing::where('student_id', $student->id)
                ->where('status', 'returned')
                ->where('hidden_by_student', false)
                ->count(),
        ];

        // ===== PEMINJAMAN AKTIF =====
        $activeBorrowings = Borrowing::with('item')
            ->where('student_id', $student->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('hidden_by_student', false)  // ← FIX: Tambahkan ini
            ->latest()
            ->take(5)
            ->get();

        // ===== BARANG TERBARU =====
        $latestItems = Item::where('status', 'tersedia')
            ->latest()
            ->take(6)
            ->get()
            ->filter(function($item) {
                // Filter hanya barang yang masih ada qty tersedia
                return $item->available_quantity > 0;
            });

        return view('mahasiswa.dashboard', compact('stats', 'activeBorrowings', 'latestItems'));
    }
}