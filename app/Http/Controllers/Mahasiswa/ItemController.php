<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // ===== DAFTAR BARANG =====
    public function index(Request $request)
    {
        $query = Item::query();

        // Filter berdasarkan kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan status (hanya tersedia)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('item_code', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        $items = $query->latest()->paginate(8);

        // Ambil kategori unik untuk filter
        $categories = Item::distinct('category')->pluck('category');

        return view('mahasiswa.items.index', compact('items', 'categories'));
    }

    // ===== DETAIL BARANG =====
    public function show($id)
    {
        $item = Item::findOrFail($id);
        return view('mahasiswa.items.show', compact('item'));
    }

    // ===== SCAN QR CODE =====
    public function scan()
    {
        return view('mahasiswa.items.scan');
    }

    // ===== PROSES HASIL SCAN QR =====
    public function scanResult(Request $request)
    {
        $qrData = $request->input('qr_data');
        
        try {
            $data = json_decode($qrData, true);
            
            if (isset($data['item_code'])) {
                $item = Item::where('item_code', $data['item_code'])->first();
                
                if ($item) {
                    return response()->json([
                        'success' => true,
                        'item' => [
                            'id' => $item->id,
                            'item_code' => $item->item_code,
                            'name' => $item->name,
                            'category' => $item->category,
                            'status' => $item->status,
                            'quantity' => $item->quantity,
                            'location' => $item->location,
                            'photo' => $item->photo ? asset('storage/' . $item->photo) : null,
                        ],
                        'redirect_url' => route('mahasiswa.items.show', $item->id)
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan!'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid!'
            ], 400);
        }
    }
}