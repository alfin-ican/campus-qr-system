<?php

namespace App\Exports;

use App\Models\Borrowing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BorrowingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Query data borrowings
     */
    public function collection()
    {
        $query = Borrowing::with(['student', 'item', 'approver'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Filter berdasarkan tanggal
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('borrow_date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('borrow_date', '<=', $this->filters['date_to']);
        }

        return $query->get();
    }

    /**
     * Header kolom
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Peminjaman',
            'NIM',
            'Nama Mahasiswa',
            'Jurusan',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Jumlah',
            'Tanggal Pinjam',
            'Rencana Kembali',
            'Tanggal Kembali',
            'Status',
            'Tujuan',
            'Disetujui Oleh',
            'Tanggal Disetujui',
            'Alasan Penolakan',
        ];
    }

    /**
     * Mapping data per row
     */
    public function map($borrowing): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $borrowing->borrowing_code,
            $borrowing->student->student_id ?? '-',
            $borrowing->student->name ?? '-',
            $borrowing->student->major ?? '-',
            $borrowing->item->item_code ?? '-',
            $borrowing->item->name ?? '-',
            $borrowing->item->category ?? '-',
            $borrowing->quantity,
            $borrowing->borrow_date->format('d/m/Y'),
            $borrowing->planned_return_date->format('d/m/Y'),
            $borrowing->return_date ? $borrowing->return_date->format('d/m/Y') : '-',
            $this->getStatusLabel($borrowing->status),
            $borrowing->purpose ?? '-',
            $borrowing->approver->name ?? '-',
            $borrowing->approved_at ? $borrowing->approved_at->format('d/m/Y H:i') : '-',
            $borrowing->rejection_reason ?? '-',
        ];
    }

    /**
     * Styling Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 18,  // Kode Peminjaman
            'C' => 12,  // NIM
            'D' => 25,  // Nama Mahasiswa
            'E' => 25,  // Jurusan
            'F' => 15,  // Kode Barang
            'G' => 25,  // Nama Barang
            'H' => 15,  // Kategori
            'I' => 10,  // Jumlah
            'J' => 15,  // Tanggal Pinjam
            'K' => 15,  // Rencana Kembali
            'L' => 15,  // Tanggal Kembali
            'M' => 15,  // Status
            'N' => 30,  // Tujuan
            'O' => 20,  // Disetujui Oleh
            'P' => 18,  // Tanggal Disetujui
            'Q' => 30,  // Alasan Penolakan
        ];
    }

    /**
     * Helper: Get status label
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'returned' => 'Dikembalikan',
            'late' => 'Terlambat',
        ];

        return $labels[$status] ?? $status;
    }
}