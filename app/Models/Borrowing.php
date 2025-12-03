<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borrowing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'borrowing_code',
        'student_id',
        'item_id',
        'quantity',              // ← Tambahan
        'borrow_date',
        'return_date',
        'planned_return_date',
        'status',
        'purpose',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'hidden_by_admin',
        'hidden_by_student'
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'return_date' => 'date',
        'planned_return_date' => 'date',
        'approved_at' => 'datetime',
        'hidden_by_admin' => 'boolean',
        'hidden_by_student' => 'boolean',
        'quantity' => 'integer',    // ← Tambahan
    ];

    // ===== AUTO GENERATE BORROWING CODE =====
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($borrowing) {
            if (empty($borrowing->borrowing_code)) {
                $date = now()->format('Ymd');
                $lastBorrowing = static::withTrashed()
                    ->whereDate('created_at', now())
                    ->orderBy('id', 'desc')
                    ->first();
                
                $number = $lastBorrowing 
                    ? intval(substr($lastBorrowing->borrowing_code, -4)) + 1 
                    : 1;
                
                $borrowing->borrowing_code = 'BRW' . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // ===== RELATIONSHIPS =====
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    // ===== SCOPES =====
    public function scopeVisibleToAdmin($query)
    {
        return $query->where('hidden_by_admin', false);
    }

    public function scopeVisibleToStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId)
                     ->where('hidden_by_student', false);
    }

    // ===== ACCESSORS =====
    public function getIsLateAttribute()
    {
        if ($this->status === 'approved' && !$this->return_date) {
            return now()->gt($this->planned_return_date);
        }
        return false;
    }
}