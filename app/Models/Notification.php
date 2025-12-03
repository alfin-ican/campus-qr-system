<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'title',
        'message',
        'type',
        'icon',
        'url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship to Admin
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id')->where('user_type', 'admin');
    }

    /**
     * Relationship to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'user_id')->where('user_type', 'student');
    }

    /**
     * Scope untuk filter notifikasi yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope untuk filter notifikasi yang sudah dibaca
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope untuk filter berdasarkan user type
     */
    public function scopeForUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeForUser($query, $userId, $userType)
    {
        return $query->where('user_id', $userId)
                     ->where('user_type', $userType);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Static method untuk create notification
     */
    public static function createNotification($userId, $userType, $title, $message, $type = null, $icon = 'bell', $url = null)
    {
        return self::create([
            'user_id' => $userId,
            'user_type' => $userType,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'url' => $url,
        ]);
    }
}