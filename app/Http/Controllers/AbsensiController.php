<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman absensi
     */
    public function index()
    {
        // Mengambil semua data absensi untuk ditampilkan di tabel rekap
        $absensis = Absensi::with('karyawan')->latest()->get();
        
        // Ambil data absen user login hari ini untuk pengecekan di view
        $karyawan = Auth::user()->karyawan;
        $absenHariIni = null;
        if ($karyawan) {
            $absenHariIni = Absensi::where('karyawan_id', $karyawan->id)
                                   ->whereDate('tanggal', Carbon::today())
                                   ->first();
        }

        return view('absensi.index', compact('absensis', 'absenHariIni'));
    }

    /**
     * Logika untuk mencatat Absen Masuk
     */
    public function store(Request $request)
    {
        // MENGAMBIL DATA BERDASARKAN USER YANG LOGIN
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Profil karyawan kamu belum diatur. Hubungi admin!');
        }

        $hariIni = Carbon::today()->format('Y-m-d');

        // 1. Cek apakah karyawan sudah absen hari ini
        $sudahAbsen = Absensi::where('karyawan_id', $karyawan->id)
                             ->whereDate('tanggal', $hariIni)
                             ->exists();

        if ($sudahAbsen) {
            return redirect()->back()->with('error', 'Opps! Kamu sudah melakukan absen masuk hari ini.');
        }

        // 2. Simpan data absen ke database
        Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal'     => $hariIni,
            'jam_masuk'   => Carbon::now()->format('H:i:s'),
            'status'      => 'Hadir',
        ]);

        return redirect()->back()->with('success', 'Berhasil! Selamat bekerja, ' . Auth::user()->name);
    }

    /**
     * Logika untuk mencatat Absen Pulang (Minimal Jam 17:00)
     */
    public function update(Request $request)
    {
        // MENGAMBIL DATA BERDASARKAN USER YANG LOGIN
        $karyawan = Auth::user()->karyawan;
        $hariIni = Carbon::today()->format('Y-m-d');

        // 1. Validasi: Cek apakah sekarang sudah jam 5 sore (17:00)
        if (Carbon::now()->hour < 17) {
            return redirect()->back()->with('error', 'Belum waktunya pulang! Kantor PT Saltek tutup jam 17:00.');
        }

        // 2. Cari data absen masuk user ini hari ini yang jam pulangnya masih kosong
        $absensi = Absensi::where('karyawan_id', $karyawan->id)
                          ->whereDate('tanggal', $hariIni)
                          ->whereNull('jam_pulang')
                          ->first();

        if (!$absensi) {
            return redirect()->back()->with('error', 'Data absen masuk tidak ditemukan atau kamu sudah absen pulang.');
        }

        // 3. Update kolom jam_pulang
        $absensi->update([
            'jam_pulang' => Carbon::now()->format('H:i:s')
        ]);

        return redirect()->back()->with('success', 'Hati-hati di jalan! Absen pulang berhasil dicatat.');
    }
}