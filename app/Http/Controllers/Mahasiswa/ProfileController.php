<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show()
    {
        $student = Auth::guard('student')->user();
        return view('mahasiswa.profile.show', compact('student'));
    }

    public function update(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'major' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student->update($request->only(['name', 'email', 'phone', 'major']));

        return redirect()->back()
            ->with('success', 'Profil berhasil diupdate!');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $student = Auth::guard('student')->user();

        if (!Hash::check($request->current_password, $student->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai!']);
        }

        $student->update(['password' => Hash::make($request->password)]);

        return redirect()->back()
            ->with('success', 'Password berhasil diubah!');
    }

    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $student = Auth::guard('student')->user();

        // Hapus foto lama
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        // Upload foto baru
        $photo = $request->file('photo');
        $photoPath = $photo->store('students', 'public');

        $student->update(['photo' => $photoPath]);

        return redirect()->back()
            ->with('success', 'Foto profil berhasil diupdate!');
    }
}