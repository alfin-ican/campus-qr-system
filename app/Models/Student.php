<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $guard = 'student';

    protected $fillable = [
        'student_id',
        'name',
        'email',
        'phone',
        'password',
        'major',
        'photo',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
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