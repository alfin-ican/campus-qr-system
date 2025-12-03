<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Barang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #333;
        }
        .header p {
            font-size: 14px;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #4a5568;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-pending { background-color: #fed7aa; color: #92400e; }
        .badge-approved { background-color: #d1fae5; color: #065f46; }
        .badge-rejected { background-color: #fecaca; color: #991b1b; }
        .badge-returned { background-color: #dbeafe; color: #1e40af; }
        .badge-late { background-color: #e9d5ff; color: #6b21a8; }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .footer p {
            margin-bottom: 50px;
        }
        .signature {
            text-align: right;
            margin-top: 60px;
        }
        .signature p {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PEMINJAMAN BARANG</h1>
        <p>Sistem Manajemen Barang Kampus dengan QR Code</p>
    </div>

    <div class="info">
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d F Y H:i') }}</p>
        <p><strong>Dicetak Oleh:</strong> {{ Auth::guard('admin')->user()->name }}</p>
        <p><strong>Total Data:</strong> {{ $borrowings->count() }} peminjaman</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode Peminjaman</th>
                <th width="20%">Mahasiswa</th>
                <th width="20%">Barang</th>
                <th width="12%">Tgl Pinjam</th>
                <th width="12%">Tgl Kembali</th>
                <th width="16%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrowings as $index => $borrowing)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $borrowing->borrowing_code }}</td>
                    <td>
                        <strong>{{ $borrowing->student->name }}</strong><br>
                        <small>{{ $borrowing->student->student_id }}</small>
                    </td>
                    <td>
                        <strong>{{ $borrowing->item->name }}</strong><br>
                        <small>{{ $borrowing->item->item_code }}</small>
                    </td>
                    <td>{{ $borrowing->borrow_date->format('d/m/Y') }}</td>
                    <td>{{ $borrowing->return_date ? $borrowing->return_date->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if($borrowing->status == 'pending')
                            <span class="badge badge-pending">Pending</span>
                        @elseif($borrowing->status == 'approved')
                            <span class="badge badge-approved">Disetujui</span>
                        @elseif($borrowing->status == 'rejected')
                            <span class="badge badge-rejected">Ditolak</span>
                        @elseif($borrowing->status == 'returned')
                            <span class="badge badge-returned">Dikembalikan</span>
                        @elseif($borrowing->status == 'late')
                            <span class="badge badge-late">Terlambat</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p>Surabaya, {{ now()->format('d F Y') }}</p>
        <p>Mengetahui,</p>
        <br><br><br>
        <p><strong>{{ Auth::guard('admin')->user()->name }}</strong></p>
        <p>{{ Auth::guard('admin')->user()->role == 'admin' ? 'Admin' : 'Petugas' }}</p>
    </div>
</body>
</html>