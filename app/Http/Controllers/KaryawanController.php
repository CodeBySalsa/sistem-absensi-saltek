<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KaryawanController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today()->format('Y-m-d');

        // --- LOGIKA STATISTIK UNTUK ADMIN ---
        
        // 1. Total seluruh anggota yang terdaftar
        $totalKaryawan = Karyawan::count();
        
        // 2. Jumlah yang sudah absen masuk atau selesai hari ini
        $hadirHariIni = Absensi::whereDate('tanggal', $hariIni)
                            ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                            ->count();
                            
        // 3. Jumlah yang mengirim laporan Izin atau Sakit hari ini
        $izinSakit = Absensi::whereDate('tanggal', $hariIni)
                            ->whereIn('status', ['Izin', 'Sakit'])
                            ->count();

        // Mengambil semua data karyawan dari database untuk tabel
        $karyawans = Karyawan::with('user')->latest()->get();

        // Mengirim semua data ke tampilan
        return view('karyawan.index', compact(
            'karyawans', 
            'totalKaryawan', 
            'hadirHariIni', 
            'izinSakit'
        ));
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
            'nip' => 'required|unique:karyawans,nip',
            'no_hp' => 'nullable', // Tambahkan validasi No HP (boleh kosong)
        ]);

        Karyawan::create($request->all());

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan!');
    }

    // --- BAGIAN EDIT, UPDATE, DESTROY ---

    // Fungsi untuk menampilkan form edit
    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $users = User::all(); 
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
            'nip' => 'required|unique:karyawans,nip,' . $id,
            'no_hp' => 'nullable', // Pastikan no_hp masuk validasi agar bisa di-update
        ]);

        // Menggunakan $request->all() akan otomatis mengambil no_hp dari form edit.blade.php
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