<?php

namespace App\Http\Controllers;

use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student()->with('user', 'parent')->first();

        if (!$student) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        return view('profile.student', compact('student'));
    }

    public function updateUser(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed', // opsional, tapi harus sesuai konfirmasi
        ]);

        $data = $request->only(['name', 'email']);

        // Jika password diisi, update juga
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Data akun berhasil diperbarui.');
    }

    public function updateStudent(Request $request)
    {
        $student = Auth::user()->student;
        // dd($student);
        $request->validate([
            'nisn' => 'required|string|max:20',
            // 'class' => 'required|string|max:20',
            'birth_date' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'religion' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'district' => 'nullable|string|max:50',
            'sub_district' => 'nullable|string|max:50',
            'village' => 'nullable|string|max:50',
            'origin_school_name' => 'nullable|string|max:100',
            'origin_school_address' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|digits:4',
        ]);

        $student->update($request->all());

        return back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function updateParent(Request $request)
    {
        $student = Auth::user()->student;

        $request->validate([
            'father_name' => 'nullable|string|max:100',
            'father_phone' => 'nullable|string|max:20',
            'father_job' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'mother_phone' => 'nullable|string|max:20',
            'mother_job' => 'nullable|string|max:100',
        ]);

        // Jika parent belum ada, buat baru
        if (!$student->parent) {
            $student->parent = new StudentParent();
            $student->parent->student_id = $student->id;
        }

        $student->parent->fill($request->all())->save();

        return back()->with('success', 'Data orang tua berhasil diperbarui.');
    }
}
