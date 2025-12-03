<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ItemController as AdminItemController;
use App\Http\Controllers\Admin\BorrowingController as AdminBorrowingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;

use App\Http\Controllers\Mahasiswa\AuthController as MahasiswaAuthController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\ItemController as MahasiswaItemController;
use App\Http\Controllers\Mahasiswa\BorrowingController as MahasiswaBorrowingController;
use App\Http\Controllers\Mahasiswa\ProfileController as MahasiswaProfileController;

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Home Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    
    if (Auth::guard('student')->check()) {
        return redirect()->route('mahasiswa.dashboard');
    }
    
    return view('home');
})->name('home')->middleware(['lock.homepage', 'prevent.back']);

/*
|--------------------------------------------------------------------------
| Notification Routes (Shared for Both Guards)
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth'])->group(function () {
//     // Halaman Notifikasi (akan auto-detect guard di controller)
//     Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    
//     // API untuk get notifikasi (JSON)
//     Route::get('/notifications/get', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    
//     // Mark as read & redirect
//     Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsReadAndRedirect'])->name('notifications.read');
    
//     // Mark all as read
//     Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
//     // Delete notifikasi
//     Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
// });

// ==================== ADMIN =====================
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/get', [NotificationController::class, 'getNotifications'])
        ->name('notifications.get');

    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsReadAndRedirect'])
        ->name('notifications.read');

    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-read');

    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
});


// ==================== STUDENT =====================
Route::middleware(['auth:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/get', [NotificationController::class, 'getNotifications'])
        ->name('notifications.get');

    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsReadAndRedirect'])
        ->name('notifications.read');

    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-read');

    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin/Petugas Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Authentication Routes (Guest Only)
    Route::middleware(['guest:admin', 'prevent.back'])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
        Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AdminAuthController::class, 'register'])->name('register.post');
    });

    // Protected Routes (Authenticated Admin Only)
    Route::middleware(['admin', 'prevent.back', 'log.admin'])->group(function () {
        
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Item Management
        Route::resource('items', AdminItemController::class);
        Route::post('/items/{id}/generate-qr', [AdminItemController::class, 'generateQrCode'])->name('items.generate-qr');
        Route::get('/items/{id}/download-qr', [AdminItemController::class, 'downloadQrCode'])->name('items.download-qr');
        Route::post('/items/{id}/update-status', [AdminItemController::class, 'updateStatus'])->name('items.update-status');
        
        // Borrowing Management
        Route::get('/borrowings', [AdminBorrowingController::class, 'index'])->name('borrowings.index');
        Route::get('/borrowings/trash', [AdminBorrowingController::class, 'trash'])->name('borrowings.trash');
        Route::get('/borrowings/{id}', [AdminBorrowingController::class, 'show'])->name('borrowings.show');
        Route::post('/borrowings/{id}/approve', [AdminBorrowingController::class, 'approve'])->name('borrowings.approve');
        Route::post('/borrowings/{id}/reject', [AdminBorrowingController::class, 'reject'])->name('borrowings.reject');
        Route::post('/borrowings/{id}/return', [AdminBorrowingController::class, 'returnItem'])->name('borrowings.return');

        // Delete (Hide) routes
        Route::delete('/borrowings/{id}', [AdminBorrowingController::class, 'destroy'])->name('borrowings.destroy');
        Route::post('/borrowings/destroy-all', [AdminBorrowingController::class, 'destroyAll'])->name('borrowings.destroy-all');
        Route::post('/borrowings/destroy-selected', [AdminBorrowingController::class, 'destroySelected'])->name('borrowings.destroy-selected');

        // Restore routes
        Route::post('/borrowings/{id}/restore', [AdminBorrowingController::class, 'restore'])->name('borrowings.restore');
        Route::post('/borrowings/restore-all', [AdminBorrowingController::class, 'restoreAll'])->name('borrowings.restore-all');
        
        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        
        // User Management (Only Admin Role)
        Route::prefix('users')->name('users.')->middleware('admin.role:admin')->group(function () {
            Route::get('/admins', [UserController::class, 'indexAdmins'])->name('admins.index');
            Route::get('/admins/{id}', [UserController::class, 'showAdmin'])->name('admins.show');
            Route::put('/admins/{id}', [UserController::class, 'updateAdmin'])->name('admins.update');
            Route::delete('/admins/{id}', [UserController::class, 'destroyAdmin'])->name('admins.destroy');
            Route::post('/admins/{id}/toggle-status', [UserController::class, 'toggleAdminStatus'])->name('admins.toggle-status');
            
            Route::get('/students', [UserController::class, 'indexStudents'])->name('students.index');
            Route::get('/students/{id}', [UserController::class, 'showStudent'])->name('students.show');
            Route::put('/students/{id}', [UserController::class, 'updateStudent'])->name('students.update');
            Route::delete('/students/{id}', [UserController::class, 'destroyStudent'])->name('students.destroy');
            Route::post('/students/{id}/toggle-status', [UserController::class, 'toggleStudentStatus'])->name('students.toggle-status');
        });
        
        // Profile Management
        Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/photo', [AdminProfileController::class, 'updatePhoto'])->name('profile.update-photo');
    });
});

/*
|--------------------------------------------------------------------------
| Mahasiswa Routes
|--------------------------------------------------------------------------
*/
Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    
    // Authentication Routes (Guest Only)
    Route::middleware(['guest:student', 'prevent.back'])->group(function () {
        Route::get('/login', [MahasiswaAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [MahasiswaAuthController::class, 'login'])->name('login.post');
        Route::get('/register', [MahasiswaAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [MahasiswaAuthController::class, 'register'])->name('register.post');
    });

    // Protected Routes (Authenticated Student Only)
    Route::middleware(['student', 'prevent.back'])->group(function () {
        
        Route::post('/logout', [MahasiswaAuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
        
        // Items (View Only)
        Route::get('/items', [MahasiswaItemController::class, 'index'])->name('items.index');
        Route::get('/items/{id}', [MahasiswaItemController::class, 'show'])->name('items.show');
        
        // QR Code Scanner
        Route::get('/scan', [MahasiswaItemController::class, 'scan'])->name('scan');
        Route::post('/scan/result', [MahasiswaItemController::class, 'scanResult'])->name('scan.result');
        
        // Borrowings
        Route::get('/borrowings', [MahasiswaBorrowingController::class, 'index'])->name('borrowings.index');
        Route::get('/borrowings/create/{item_id?}', [MahasiswaBorrowingController::class, 'create'])->name('borrowings.create');
        Route::post('/borrowings', [MahasiswaBorrowingController::class, 'store'])->name('borrowings.store');
        Route::get('/borrowings/{id}', [MahasiswaBorrowingController::class, 'show'])->name('borrowings.show');
        Route::delete('/borrowings/{id}/cancel', [MahasiswaBorrowingController::class, 'cancel'])->name('borrowings.cancel');
        
        // DELETE - Hapus riwayat peminjaman
        Route::delete('/borrowings/{id}', [MahasiswaBorrowingController::class, 'destroy'])->name('borrowings.destroy');
        Route::post('/borrowings/destroy-selected', [MahasiswaBorrowingController::class, 'destroySelected'])->name('borrowings.destroy-selected');
        Route::post('/borrowings/destroy-all-history', [MahasiswaBorrowingController::class, 'destroyAllHistory'])->name('borrowings.destroy-all-history');
        
        // Profile
        Route::get('/profile', [MahasiswaProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [MahasiswaProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [MahasiswaProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/photo', [MahasiswaProfileController::class, 'updatePhoto'])->name('profile.update-photo');
    });
});