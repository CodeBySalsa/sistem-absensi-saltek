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
        // Kita ambil data user yang belum jadi karyawan untuk pilihan di form
        $users = User::all();
        return view('karyawan.create', compact('users'));
    }

    // Fungsi untuk menyimpan data karyawan baru ke database
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
}