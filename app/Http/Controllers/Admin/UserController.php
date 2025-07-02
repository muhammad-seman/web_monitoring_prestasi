<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Tampilkan semua user
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
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
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,kepala,pegawai',
            'status'   => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);
        ActivityLogger::log('create', 'user', 'Tambah user: ' . $request->name);

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
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,kepala,pegawai',
            'status'   => 'required|in:active,inactive',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        ActivityLogger::log('update', 'user', 'Update user: ' . $user->name);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    // Hapus user
    public function destroy(User $user)
    {
        $userName = $user->name;
        $user->delete();
        ActivityLogger::log('delete', 'user', 'Hapus user: ' . $userName);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
