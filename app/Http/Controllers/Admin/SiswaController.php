<?php
namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        return response()->json(Siswa::with('kelas')->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn'          => 'required|unique:siswa,nisn',
            'nama'          => 'required|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'id_kelas'      => 'nullable|exists:kelas,id',
            'alamat'        => 'nullable|string',
            'tahun_masuk'   => 'nullable|integer|min:2000|max:2100',
        ]);
        $siswa = Siswa::create($validated);
        return response()->json($siswa, 201);
    }

    public function show($id)
    {
        return response()->json(Siswa::with('kelas')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $validated = $request->validate([
            'nisn'          => 'sometimes|required|unique:siswa,nisn,'.$siswa->id,
            'nama'          => 'sometimes|required|max:100',
            'jenis_kelamin' => 'sometimes|required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'id_kelas'      => 'nullable|exists:kelas,id',
            'alamat'        => 'nullable|string',
            'tahun_masuk'   => 'nullable|integer|min:2000|max:2100',
        ]);
        $siswa->update($validated);
        return response()->json($siswa);
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();
        return response()->json(['message' => 'Siswa deleted']);
    }
}