<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrowing;
use App\Models\Admin;
use App\Helpers\NotificationHelper;
use App\Mail\BorrowingReminderMail;
use App\Mail\BorrowingLateMail;
use Mail;

class BorrowingReminderCommand extends Command
{
    protected $signature = 'borrowing:process-reminders';
    protected $description = 'Send reminder & late notifications for borrowings';

    public function handle()
    {
        $this->sendOneHourReminder();
        $this->sendLateWarning();
        $this->notifyAdminsLate();

        return Command::SUCCESS;
    }

    /**
     * ✔ Reminder 1 jam sebelum deadline
     */
    private function sendOneHourReminder()
    {
        $borrowings = Borrowing::where('status', 'approved')
            ->where('reminder_sent', false)
            ->whereRaw("TIMESTAMPDIFF(MINUTE, NOW(), return_deadline) BETWEEN 0 AND 60")
            ->get();

        foreach ($borrowings as $b) {
            // === Notifikasi platform ===
            NotificationHelper::notifyStudent(
                $b->student_id,
                'Pengembalian Barang 1 Jam Lagi',
                "Barang '{$b->item->name}' harus dikembalikan sebelum " . 
                    $b->return_deadline->format('H:i'),
                'peringatan_pengembalian',
                route('student.borrowings.show', $b->id)
            );

            // === Email ===
            Mail::to($b->student->email)->send(new BorrowingReminderMail($b));

            $b->reminder_sent = true;
            $b->save();
        }
    }

    /**
     * ✔ Reminder ketika telat
     */
    private function sendLateWarning()
    {
        $borrowings = Borrowing::where('status', 'approved')
            ->where('late_warning_sent', false)
            ->where('return_deadline', '<', now())
            ->get();

        foreach ($borrowings as $b) {
            NotificationHelper::notifyStudent(
                $b->student_id,
                'Peringatan Keterlambatan Pengembalian',
                "Kamu terlambat mengembalikan barang '{$b->item->name}'. Segera kembalikan!",
                'telat',
                route('student.borrowings.show', $b->id)
            );

            Mail::to($b->student->email)->send(new BorrowingLateMail($b));

            $b->late_warning_sent = true;
            $b->save();
        }
    }

    /**
     * ✔ Kirim notif ke semua admin kalau ada mahasiswa telat
     */
    private function notifyAdminsLate()
    {
        $lateBorrowings = Borrowing::where('status', 'approved')
            ->where('return_deadline', '<', now())
            ->where('late_warning_sent', true) // Sudah diperingatkan
            ->get();

        if ($lateBorrowings->isEmpty()) return;

        $admins = Admin::all();

        foreach ($lateBorrowings as $b) {
            foreach ($admins as $admin) {
                NotificationHelper::notifyAdmin(
                    $admin->id,
                    'Mahasiswa Terlambat Mengembalikan Barang',
                    "Mahasiswa {$b->student->name} terlambat mengembalikan barang '{$b->item->name}'.",
                    'telat',
                    route('admin.borrowings.show', $b->id)
                );
            }
        }
    }
}
