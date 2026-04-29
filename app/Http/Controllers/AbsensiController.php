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
        $hariIni = Carbon::today()->format('Y-m-d');

        // Otomatis menutup sesi yang "menggantung" dari hari kemarin
        Absensi::where('tanggal', '<', $hariIni)
                ->whereNull('jam_pulang')
                ->where('status', 'Hadir')
                ->update(['status' => 'Selesai']);

        // Mengambil data absensi dengan relasi user dan karyawan untuk rekap
        $absensis = Absensi::with(['karyawan', 'user'])->latest()->get();
        
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
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Profil karyawan kamu belum diatur. Hubungi admin!');
        }

        $hariIni = Carbon::today()->format('Y-m-d');

        // Cek apakah karyawan sudah absen hari ini
        $sudahAbsen = Absensi::where('karyawan_id', $karyawan->id)
                             ->whereDate('tanggal', $hariIni)
                             ->exists();

        if ($sudahAbsen) {
            return redirect()->back()->with('error', 'Opps! Kamu sudah melakukan absen masuk hari ini.');
        }

        // Simpan data absen ke database
        Absensi::create([
            'user_id'     => Auth::id(), // BARIS PENTING: Agar nama tidak muncul "Data Kosong"
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
        $karyawan = Auth::user()->karyawan;
        $hariIni = Carbon::today()->format('Y-m-d');

        // Validasi: Kantor PT Saltek tutup jam 17:00
        if (Carbon::now()->hour < 17) {
            return redirect()->back()->with('error', 'Belum waktunya pulang! Tombol pulang aktif jam 17:00.');
        }

        // Cari data absen hari ini yang belum pulang
        $absensi = Absensi::where('karyawan_id', $karyawan->id)
                          ->whereDate('tanggal', $hariIni)
                          ->whereNull('jam_pulang')
                          ->first();

        if (!$absensi) {
            return redirect()->back()->with('error', 'Data absen tidak ditemukan atau kamu sudah absen pulang.');
        }

        // Update jam_pulang dan set status jadi Selesai
        $absensi->update([
            'jam_pulang' => Carbon::now()->format('H:i:s'),
            'status'     => 'Selesai'
        ]);

        return redirect()->back()->with('success', 'Hati-hati di jalan! Absen pulang berhasil dicatat.');
    }
}