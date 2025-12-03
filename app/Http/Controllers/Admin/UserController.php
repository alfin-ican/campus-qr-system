<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // ===== ADMIN MANAGEMENT =====
    
    public function indexAdmins(Request $request)
    {
        $query = Admin::query();

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('admin_id', 'like', '%' . $request->search . '%');
            });
        }

        $admins = $query->latest()->paginate(15);

        return view('admin.users.admins.index', compact('admins'));
    }

    public function showAdmin($id)
    {
        $admin = Admin::withCount('items', 'approvedBorrowings')->findOrFail($id);
        return view('admin.users.admins.show', compact('admin'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,petugas',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $admin->update($request->only(['name', 'email', 'phone', 'role']));

        return redirect()->back()
            ->with('success', 'Data admin berhasil diupdate!');
    }

    public function destroyAdmin($id)
    {
        $admin = Admin::findOrFail($id);

        // Tidak bisa hapus diri sendiri
        if ($admin->id == auth()->guard('admin')->id()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $admin->delete();

        return redirect()->route('admin.users.admins.index')
            ->with('success', 'Admin berhasil dihapus!');
    }

    public function toggleAdminStatus($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->update(['is_active' => !$admin->is_active]);

        $status = $admin->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Admin berhasil {$status}!");
    }

    // ===== STUDENT MANAGEMENT =====
    
    public function indexStudents(Request $request)
    {
        $query = Student::withCount('borrowings');

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('student_id', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->latest()->paginate(15);

        return view('admin.users.students.index', compact('students'));
    }

    public function showStudent($id)
    {
        $student = Student::with('borrowings.item')->findOrFail($id);
        return view('admin.users.students.show', compact('student'));
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'major' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student->update($request->only(['name', 'email', 'phone', 'major']));

        return redirect()->back()
            ->with('success', 'Data mahasiswa berhasil diupdate!');
    }

    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);

        // Cek apakah ada peminjaman aktif
        $activeBorrowing = $student->borrowings()
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($activeBorrowing) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus mahasiswa yang memiliki peminjaman aktif!');
        }

        $student->delete();

        return redirect()->route('admin.users.students.index')
            ->with('success', 'Mahasiswa berhasil dihapus!');
    }

    public function toggleStudentStatus($id)
    {
        $student = Student::findOrFail($id);
        $student->update(['is_active' => !$student->is_active]);

        $status = $student->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Mahasiswa berhasil {$status}!");
    }
}