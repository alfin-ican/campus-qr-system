<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    /**
     * Create notification untuk admin
     */
    public static function notifyAdmin($adminId, $title, $message, $type = null, $url = null)
    {
        return Notification::create([
            'user_id' => $adminId,
            'user_type' => 'admin',
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => $url,
            'icon' => self::getIcon($type),
        ]);
    }

    /**
     * Create notification untuk student
     */
    public static function notifyStudent($studentId, $title, $message, $type = null, $url = null)
    {
        return Notification::create([
            'user_id' => $studentId,
            'user_type' => 'student',
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => $url,
            'icon' => self::getIcon($type),
        ]);
    }

    /**
     * Notify semua admin
     */
    public static function notifyAllAdmins($title, $message, $type = null, $url = null)
    {
        $admins = \App\Models\Admin::where('is_active', true)->get();
        
        foreach ($admins as $admin) {
            self::notifyAdmin($admin->id, $title, $message, $type, $url);
        }
    }

    /**
     * Get icon based on notification type
     */
    private static function getIcon($type)
    {
        return match($type) {
            'peminjaman_baru' => 'clipboard-list',
            'approve' => 'check-circle',
            'tolak' => 'x-circle',
            'peringatan_pengembalian' => 'alert-triangle',
            default => 'bell',
        };
    }

    /**
     * Notification untuk peminjaman baru (ke admin)
     */
    public static function newBorrowingRequest($borrowing)
    {
        $message = "{$borrowing->student->name} mengajukan peminjaman {$borrowing->item->name}";
        $url = route('admin.borrowings.show', $borrowing->id);
        
        self::notifyAllAdmins(
            'Peminjaman Baru',
            $message,
            'peminjaman_baru',
            $url
        );
    }

    /**
     * Notification untuk approval (ke mahasiswa)
     */
    public static function borrowingApproved($borrowing)
    {
        $message = "Peminjaman {$borrowing->item->name} Anda telah disetujui. Silakan ambil barang sesuai jadwal.";
        $url = route('mahasiswa.borrowings.show', $borrowing->id);
        
        self::notifyStudent(
            $borrowing->student_id,
            'Peminjaman Disetujui',
            $message,
            'approve',
            $url
        );
    }

    /**
     * Notification untuk rejection (ke mahasiswa)
     */
    public static function borrowingRejected($borrowing, $reason = null)
    {
        $message = "Peminjaman {$borrowing->item->name} Anda ditolak.";
        if ($reason) {
            $message .= " Alasan: {$reason}";
        }
        $url = route('mahasiswa.borrowings.show', $borrowing->id);
        
        self::notifyStudent(
            $borrowing->student_id,
            'Peminjaman Ditolak',
            $message,
            'tolak',
            $url
        );
    }

    /**
     * Notification untuk peringatan pengembalian
     */
    public static function returnReminder($borrowing, $daysLeft)
    {
        $message = "Pengembalian {$borrowing->item->name} akan jatuh tempo dalam {$daysLeft} hari. Mohon segera dikembalikan.";
        $url = route('mahasiswa.borrowings.show', $borrowing->id);
        
        self::notifyStudent(
            $borrowing->student_id,
            'Peringatan Pengembalian',
            $message,
            'peringatan_pengembalian',
            $url
        );
    }

    /**
     * Notification untuk keterlambatan pengembalian
     */
    public static function overdueBorrowing($borrowing, $daysOverdue)
    {
        // Notif ke mahasiswa
        $messageStudent = "Anda terlambat mengembalikan {$borrowing->item->name} sebanyak {$daysOverdue} hari. Segera kembalikan untuk menghindari sanksi.";
        self::notifyStudent(
            $borrowing->student_id,
            'Keterlambatan Pengembalian',
            $messageStudent,
            'peringatan_pengembalian',
            route('mahasiswa.borrowings.show', $borrowing->id)
        );

        // Notif ke admin
        $messageAdmin = "{$borrowing->student->name} terlambat mengembalikan {$borrowing->item->name} ({$daysOverdue} hari).";
        self::notifyAllAdmins(
            'Keterlambatan Pengembalian',
            $messageAdmin,
            'peringatan_pengembalian',
            route('admin.borrowings.show', $borrowing->id)
        );
    }

    /**
     * Notification untuk pengembalian barang (ke admin)
     */
    public static function itemReturned($borrowing)
    {
        $message = "{$borrowing->student->name} telah mengembalikan {$borrowing->item->name}";
        $url = route('admin.borrowings.show', $borrowing->id);
        
        self::notifyAllAdmins(
            'Barang Dikembalikan',
            $message,
            'approve',
            $url
        );
    }
}