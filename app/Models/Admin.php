<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $guard = 'admin';

    protected $fillable = [
        'admin_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
        'photo',
        'is_active',
        'last_login'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Generate Admin ID otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($admin) {
            if (empty($admin->admin_id)) {
                $lastAdmin = static::withTrashed()->orderBy('id', 'desc')->first();
                $number = $lastAdmin ? intval(substr($lastAdmin->admin_id, 3)) + 1 : 1;
                $admin->admin_id = 'ADM' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function items()
    {
        return $this->hasMany(Item::class, 'created_by');
    }

    public function approvedBorrowings()
    {
        return $this->hasMany(Borrowing::class, 'approved_by');
    }
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'user');
    }

    public function unreadNotifications()
    {
        return $this->morphMany(Notification::class, 'user')->where('is_read', false);
    }

    public function getUnreadNotificationsCountAttribute()
    {
        return $this->unreadNotifications()->count();
    }
}