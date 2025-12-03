<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Borrowing;
use App\Models\Item;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // BACKEND: Ambil data statistik dari database
        $stats = [
            'total_items' => Item::count(),
            'borrowed_items' => Borrowing::where('status', 'approved')->sum('quantity'),
            'pending_borrowings' => Borrowing::where('status', 'pending')->count(),
            'total_students' => Student::count(),
        ];

        // BACKEND: Ambil 5 pengajuan peminjaman terbaru
        $recentBorrowings = Borrowing::with(['student', 'item'])
            ->latest()
            ->take(5)
            ->get();

        // ===== CHART 1: Statistik Peminjaman Bulanan =====
        $monthlyBorrowings = Borrowing::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Format data untuk Chart.js (fill missing months dengan 0)
        $monthlyData = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        for ($i = 1; $i <= 12; $i++) {
            $found = $monthlyBorrowings->firstWhere('month', $i);
            $monthlyData[] = [
                'month' => $monthNames[$i - 1],
                'total' => $found ? $found->total : 0
            ];
        }

        // ===== CHART 2: Barang Paling Sering Dipinjam =====
        $topItems = Borrowing::select('item_id', DB::raw('COUNT(*) as borrow_count'), DB::raw('SUM(quantity) as total_quantity'))
            ->with('item')
            ->groupBy('item_id')
            ->orderBy('borrow_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBorrowings', 'monthlyData', 'topItems'));
    }
}