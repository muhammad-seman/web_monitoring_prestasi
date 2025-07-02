<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        return response()->json(Kelas::with('wali')->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas'    => 'required|max:30',
            'id_wali_kelas' => 'nullable|exists:users,id',
            'tahun_ajaran'  => 'nullable|max:10',
        ]);
        $kelas = Kelas::create($validated);
        return response()->json($kelas, 201);
    }

    public function show($id)
    {
        return response()->json(Kelas::with('wali', 'siswa')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $validated = $request->validate([
            'nama_kelas'    => 'sometimes|required|max:30',
            'id_wali_kelas' => 'nullable|exists:users,id',
            'tahun_ajaran'  => 'nullable|max:10',
        ]);
        $kelas->update($validated);
        return response()->json($kelas);
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        return response()->json(['message' => 'Kelas deleted']);
    }
}