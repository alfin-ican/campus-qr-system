<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display notifications page
     * Detect guard and show appropriate view
     */
    public function index()
    {
        try {
            // Cek guard mana yang sedang login
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $userType = 'admin';
                
                $notifications = Notification::where('user_id', $user->id)
                    ->where('user_type', $userType)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                
                return view('admin.notifications.index', [
                    'notifications' => $notifications,
                    'title' => 'Semua Notifikasi'
                ]);
                
            } elseif (Auth::guard('student')->check()) {
                $user = Auth::guard('student')->user();
                $userType = 'student';
                
                $notifications = Notification::where('user_id', $user->id)
                    ->where('user_type', $userType)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                
                return view('mahasiswa.notifications.index', [
                    'notifications' => $notifications,
                    'title' => 'Semua Notifikasi'
                ]);
                
            } else {
                // Jika tidak ada yang login, redirect ke home
                return redirect()->route('home');
            }
            
        } catch (\Exception $e) {
            \Log::error('Notifications index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat notifikasi');
        }
    }

    /**
     * Get notifications via AJAX
     */
    public function getNotifications()
    {
        try {
            // Deteksi guard yang sedang login
            $user = null;
            $userType = null;
            
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $userType = 'admin';
            } elseif (Auth::guard('student')->check()) {
                $user = Auth::guard('student')->user();
                $userType = 'student';
            }
            
            if (!$user) {
                return response()->json([
                    'notifications' => [],
                    'unread_count' => 0
                ], 401);
            }
            
            // Ambil notifications
            $notifications = Notification::where('user_id', $user->id)
                ->where('user_type', $userType)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $unreadCount = Notification::where('user_id', $user->id)
                ->where('user_type', $userType)
                ->where('is_read', false)
                ->count();
            
            // Format notifications
            $formattedNotifications = $notifications->map(function($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'type' => $notif->type,
                    'icon' => $notif->icon ?? 'bell',
                    'is_read' => $notif->is_read,
                    'created_at' => $notif->created_at->toISOString(),
                ];
            });
            
            return response()->json([
                'notifications' => $formattedNotifications,
                'unread_count' => $unreadCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Notification fetch error: ' . $e->getMessage());
            return response()->json([
                'notifications' => [],
                'unread_count' => 0
            ], 200);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            // Deteksi guard yang sedang login
            $user = null;
            $userType = null;
            
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $userType = 'admin';
            } elseif (Auth::guard('student')->check()) {
                $user = Auth::guard('student')->user();
                $userType = 'student';
            }
            
            if (!$user) {
                return response()->json(['success' => false], 401);
            }
            
            // Update semua notifikasi menjadi read
            Notification::where('user_id', $user->id)
                ->where('user_type', $userType)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            \Log::error('Mark all read error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Mark notification as read and redirect to target URL
     */
    public function markAsReadAndRedirect($id)
    {
        try {
            // Deteksi guard yang sedang login
            $user = null;
            $userType = null;
            $redirectRoute = 'home';
            
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $userType = 'admin';
                $redirectRoute = 'admin.dashboard';
            } elseif (Auth::guard('student')->check()) {
                $user = Auth::guard('student')->user();
                $userType = 'student';
                $redirectRoute = 'mahasiswa.dashboard';
            }
            
            if (!$user) {
                return redirect()->route('home');
            }
            
            $notification = Notification::where('id', $id)
                ->where('user_id', $user->id)
                ->where('user_type', $userType)
                ->first();
            
            if ($notification) {
                // Mark as read
                $notification->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
                
                // Redirect ke URL yang sesuai dengan notification
                if ($notification->url) {
                    return redirect($notification->url);
                }
            }
            
            // Jika tidak ada URL, redirect ke dashboard sesuai guard
            return redirect()->route($redirectRoute);
            
        } catch (\Exception $e) {
            \Log::error('Read notification error: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        try {
            // Deteksi guard yang sedang login
            $user = null;
            $userType = null;
            
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $userType = 'admin';
            } elseif (Auth::guard('student')->check()) {
                $user = Auth::guard('student')->user();
                $userType = 'student';
            }
            
            if (!$user) {
                return response()->json(['success' => false], 401);
            }
            
            $notification = Notification::where('id', $id)
                ->where('user_id', $user->id)
                ->where('user_type', $userType)
                ->first();
            
            if ($notification) {
                $notification->delete();
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
            
        } catch (\Exception $e) {
            \Log::error('Delete notification error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
}