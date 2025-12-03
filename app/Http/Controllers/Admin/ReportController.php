<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Item;
use App\Exports\BorrowingsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // ===== TAMPILKAN HALAMAN LAPORAN =====
    public function index(Request $request)
    {
        $query = Borrowing::with(['student', 'item', 'approver']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->whereHas('item', function($q) use ($request) {
                $q->where('category', $request->category);
            });
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
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('student_id', 'like', "%{$search}%");
                  })
                  ->orWhereHas('item', function($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%");
                  });
            });
        }

        $borrowings = $query->latest()->paginate(10);

        // Statistik
        $stats = [
            'total' => Borrowing::count(),
            'pending' => Borrowing::where('status', 'pending')->count(),
            'approved' => Borrowing::where('status', 'approved')->count(),
            'returned' => Borrowing::where('status', 'returned')->count(),
            'rejected' => Borrowing::where('status', 'rejected')->count(),
            'late' => Borrowing::where('status', 'late')->count(),
        ];

        // Ambil list kategori unik dari items
        $categories = Item::distinct('category')
            ->whereNotNull('category')
            ->pluck('category')
            ->sort()
            ->values();

        return view('admin.reports.index', compact('borrowings', 'stats', 'categories'));
    }

    // ===== EXPORT KE PDF =====
    public function exportPdf(Request $request)
    {
        $filters = [
            'status' => $request->status,
            'category' => $request->category,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];

        $query = Borrowing::with(['student', 'item', 'approver'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category'])) {
            $query->whereHas('item', function($q) use ($filters) {
                $q->where('category', $filters['category']);
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('borrow_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('borrow_date', '<=', $filters['date_to']);
        }

        $borrowings = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'borrowings' => $borrowings,
            'filters' => $filters,
            'generated_at' => now(),
        ]);

        $filename = 'Laporan_Peminjaman_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    // ===== EXPORT KE EXCEL =====
    public function exportExcel(Request $request)
    {
        $filters = [
            'status' => $request->status,
            'category' => $request->category,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];

        $filename = 'Laporan_Peminjaman_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new BorrowingsExport($filters), $filename);
    }
}