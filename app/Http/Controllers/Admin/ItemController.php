<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ItemController extends Controller
{
    // ===== TAMPILKAN DAFTAR BARANG =====
    public function index(Request $request)
    {
        $query = Item::with('creator');

        // Filter berdasarkan kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('item_code', 'like', '%' . $request->search . '%');
            });
        }

        $items = $query->latest()->paginate(6);

        // Ambil kategori unik untuk filter
        $categories = Item::distinct('category')->pluck('category');

        return view('admin.items.index', compact('items', 'categories'));
    }

    // ===== TAMPILKAN FORM TAMBAH BARANG =====
    public function create()
    {
        return view('admin.items.create');
    }

    // ===== SIMPAN BARANG BARU =====
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Nama barang wajib diisi',
            'category.required' => 'Kategori wajib diisi',
            'quantity.required' => 'Jumlah wajib diisi',
            'quantity.min' => 'Jumlah minimal 1',
            'photo.image' => 'File harus berupa gambar',
            'photo.max' => 'Ukuran foto maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('photo');
        $data['created_by'] = Auth::guard('admin')->id();
        $data['status'] = 'tersedia';

        // Upload foto jika ada
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoPath = $photo->store('items', 'public');
            $data['photo'] = $photoPath;
        }

        $item = Item::create($data);

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil ditambahkan dengan kode: ' . $item->item_code);
    }

    // ===== TAMPILKAN DETAIL BARANG =====
    public function show($id)
    {
        $item = Item::with(['creator', 'borrowings.student'])->findOrFail($id);
        return view('admin.items.show', compact('item'));
    }

    // ===== TAMPILKAN FORM EDIT BARANG =====
    public function edit($id)
    {
        $item = Item::findOrFail($id);
        return view('admin.items.edit', compact('item'));
    }

    // ===== UPDATE BARANG =====
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:tersedia,dipinjam,rusak,maintenance',
            'location' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('photo');

        // Upload foto baru jika ada
        if ($request->hasFile('photo')) {
            // Hapus foto lama
            if ($item->photo && Storage::disk('public')->exists($item->photo)) {
                Storage::disk('public')->delete($item->photo);
            }

            $photo = $request->file('photo');
            $photoPath = $photo->store('items', 'public');
            $data['photo'] = $photoPath;
        }

        $item->update($data);

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil diupdate!');
    }

    // ===== HAPUS BARANG =====
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        // Cek apakah barang sedang dipinjam
        $activeBorrowing = $item->borrowings()
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($activeBorrowing) {
            return redirect()->back()
                ->with('error', 'Barang tidak dapat dihapus karena sedang dipinjam atau ada pengajuan pending!');
        }

        // Hapus foto
        if ($item->photo && Storage::disk('public')->exists($item->photo)) {
            Storage::disk('public')->delete($item->photo);
        }

        // Hapus QR Code
        if ($item->qr_code && Storage::disk('public')->exists($item->qr_code)) {
            Storage::disk('public')->delete($item->qr_code);
        }

        $item->delete();

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil dihapus!');
    }

    // ===== GENERATE QR CODE =====
    public function generateQrCode($id)
    {
        $item = Item::findOrFail($id);

        try {
            // Data yang akan di-encode ke QR Code
            $qrData = json_encode([
                'item_code' => $item->item_code,
                'name' => $item->name,
                'category' => $item->category,
                'location' => $item->location,
                'system' => 'CampusQR',
                'generated_at' => now()->toDateTimeString()
            ]);

            // Generate QR Code sebagai SVG (tidak memerlukan Imagick)
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->margin(2)
                ->errorCorrection('H')
                ->backgroundColor(255, 255, 255)
                ->color(0, 0, 0)
                ->generate($qrData);

            // Buat direktori jika belum ada
            if (!Storage::disk('public')->exists('qrcodes')) {
                Storage::disk('public')->makeDirectory('qrcodes');
            }

            // Simpan QR Code ke storage
            $qrPath = 'qrcodes/' . $item->item_code . '.svg';
            Storage::disk('public')->put($qrPath, $qrCode);

            // Update item dengan path QR Code
            $item->update(['qr_code' => $qrPath]);

            return redirect()->back()
                ->with('success', 'QR Code berhasil digenerate untuk barang: ' . $item->name);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal generate QR Code: ' . $e->getMessage());
        }
    }

    // ===== DOWNLOAD QR CODE =====
    public function downloadQrCode($id)
    {
        $item = Item::findOrFail($id);

        // Cek apakah QR Code sudah digenerate
        if (!$item->qr_code) {
            return redirect()->back()
                ->with('error', 'QR Code belum digenerate! Silakan generate terlebih dahulu.');
        }

        // Cek apakah file QR Code ada di storage
        if (!Storage::disk('public')->exists($item->qr_code)) {
            return redirect()->back()
                ->with('error', 'File QR Code tidak ditemukan! Silakan generate ulang.');
        }

        // Tentukan nama file untuk download
        $extension = pathinfo($item->qr_code, PATHINFO_EXTENSION);
        $filename = 'QRCode_' . $item->item_code . '.' . $extension;

        // Download file
        return Storage::disk('public')->download($item->qr_code, $filename);
    }

    // ===== UPDATE STATUS BARANG =====
    public function updateStatus(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:tersedia,dipinjam,rusak,maintenance',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $item->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Status barang berhasil diupdate menjadi: ' . ucfirst($request->status));
    }
}