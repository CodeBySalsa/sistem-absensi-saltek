<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        // Mengambil semua data karyawan dari database
        $karyawans = Karyawan::with('user')->get();

        // Mengirim data ke tampilan (view)
        return view('karyawan.index', compact('karyawans'));
    }

    // Fungsi untuk menampilkan halaman tambah karyawan
    public function create()
    {
        $users = User::all();
        return view('karyawan.create', compact('users'));
    }

    // Fungsi untuk menyimpan data karyawan baru
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|unique:karyawans,user_id',
            'nama_lengkap' => 'required',
            'jabatan' => 'required',
        ]);

        Karyawan::create($request->all());

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan!');
    }

    // --- BAGIAN BARU: EDIT, UPDATE, DESTROY ---

    // Fungsi untuk menampilkan form edit
    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $users = User::all(); // Diambil untuk jika ingin mengganti akun user yang terhubung
        return view('karyawan.edit', compact('karyawan', 'users'));
    }

    // Fungsi untuk menyimpan perubahan data
    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'user_id' => 'required|unique:karyawans,user_id,' . $id,
            'nama_lengkap' => 'required',
            'jabatan' => 'required',
        ]);

        $karyawan->update($request->all());

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    // Fungsi untuk menghapus data
    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan telah dihapus dari sistem.');
    }
}