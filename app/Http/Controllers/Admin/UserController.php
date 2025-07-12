<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Siswa;

class UserController extends Controller
{
    // Tampilkan semua user
    public function index()
    {
        $users = User::with(['siswa', 'anak'])->paginate(10);
        $siswa = Siswa::doesntHave('user')->pluck('nama', 'id');
        $siswaWali = Siswa::doesntHave('wali')->pluck('nama', 'id');
        return view('admin.users.index', compact('users', 'siswa', 'siswaWali'));
    }

    // Show form tambah user
    public function create()
    {
        return view('admin.users.create');
    }

    // Simpan user baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,kepala_sekolah,guru,siswa,wali',
            'status'   => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);
        ActivityLogger::log('create', 'user', 'Tambah user: ' . $request->nama);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambah.');
    }

    // Show detail user
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    // Show form edit user
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,kepala_sekolah,guru,siswa,wali',
            'status'   => 'required|in:active,inactive',
            'siswa_id' => 'nullable|exists:siswa,id',
            'anak_ids' => 'nullable|array',
            'anak_ids.*' => 'exists:siswa,id',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        // Handle assign anak untuk wali
        if ($user->role === 'wali' && $request->has('anak_ids')) {
            // Reset semua anak yang sebelumnya di-assign ke wali ini
            Siswa::where('wali_id', $user->id)->update(['wali_id' => null]);
            
            // Assign anak-anak yang dipilih
            if (!empty($request->anak_ids)) {
                Siswa::whereIn('id', $request->anak_ids)->update(['wali_id' => $user->id]);
            }
        }

        ActivityLogger::log('update', 'user', 'Update user: ' . $user->nama);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    // Hapus user
    public function destroy(User $user)
    {
        $userName = $user->nama;
        $user->delete();
        ActivityLogger::log('delete', 'user', 'Hapus user: ' . $userName);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
