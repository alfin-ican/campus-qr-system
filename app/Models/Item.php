<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'item_code',
        'category',         // ← String biasa (bukan category_id)
        'quantity',
        'status',
        'description',
        'location',
        'photo',
        'qr_code',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // ===== AUTO GENERATE ITEM CODE =====
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->item_code)) {
                // Format: ITM-YYYYMMDD-XXXX
                $date = now()->format('Ymd');
                
                $lastItem = static::withTrashed()
                    ->whereDate('created_at', now())
                    ->orderBy('id', 'desc')
                    ->first();
                
                $number = $lastItem 
                    ? intval(substr($lastItem->item_code, -4)) + 1 
                    : 1;
                
                $item->item_code = 'ITM-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // ===== RELATIONSHIPS =====
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    // ===== ACCESSORS =====
    
    /**
     * Hitung jumlah barang yang sedang dipinjam (pending + approved)
     */
    public function getBorrowedQuantityAttribute()
    {
        return $this->borrowings()
            ->whereIn('status', ['pending', 'approved'])
            ->sum('quantity');
    }

    /**
     * Hitung jumlah barang yang tersedia untuk dipinjam
     */
    public function getAvailableQuantityAttribute()
    {
        return $this->quantity - $this->borrowed_quantity;
    }

    /**
     * Cek apakah barang masih tersedia
     */
    public function getIsAvailableAttribute()
    {
        return $this->available_quantity > 0;
    }
}