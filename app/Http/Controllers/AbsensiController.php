<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman absensi
     */
    public function index()
    {
        // Mengambil semua data absensi untuk ditampilkan di tabel rekap
        $absensis = Absensi::with('karyawan')->latest()->get();
        return view('absensi.index', compact('absensis'));
    }

    /**
     * Logika untuk mencatat Absen Masuk
     */
    public function store(Request $request)
    {
        // Sementara kita kunci di ID 1 (Budi Saltek) 
        // Nanti kalau fitur login sudah jadi, ini diganti jadi Auth::id()
        $karyawanId = 1; 
        $hariIni = Carbon::today()->format('Y-m-d');

        // 1. Cek apakah karyawan sudah absen hari ini
        $sudahAbsen = Absensi::where('karyawan_id', $karyawanId)
                             ->whereDate('tanggal', $hariIni)
                             ->exists();

        if ($sudahAbsen) {
            return redirect()->back()->with('error', 'Opps! Kamu sudah melakukan absen hari ini.');
        }

        // 2. Simpan data absen ke database
        Absensi::create([
            'karyawan_id' => $karyawanId,
            'tanggal'     => $hariIni,
            'jam_masuk'   => Carbon::now()->format('H:i:s'),
            'status'      => 'Hadir', // Bisa ditambah logika terlambat jika lewat jam 08:00
        ]);

        return redirect()->back()->with('success', 'Berhasil! Absen masuk kamu sudah tercatat.');
    }
}