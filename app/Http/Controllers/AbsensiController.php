<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman riwayat absensi
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role == 'admin') {
            // Admin melihat semua riwayat karyawan
            $absensis = Absensi::with('karyawan')->latest()->get();
        } else {
            // Karyawan hanya melihat riwayat miliknya sendiri
            $absensis = Absensi::where('karyawan_id', $user->karyawan->id ?? 0)
                                ->latest()
                                ->get();
        }

        return view('absensi.index', compact('absensis'));
    }

    /**
     * Menangani Absen Masuk (Hadir) dengan Koordinat GPS
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Validasi: Memastikan data GPS terkirim dari modal
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ], [
            'latitude.required' => 'Lokasi GPS belum terdeteksi.',
            'longitude.required' => 'Lokasi GPS belum terdeteksi.',
        ]);

        // Cek apakah hari ini sudah ada data (Hadir/Izin/Sakit/Terlambat)
        $sudahInput = Absensi::where('karyawan_id', $user->karyawan->id)
                            ->where('tanggal', date('Y-m-d'))
                            ->first();

        if ($sudahInput) {
            return back()->with('error', 'Anda sudah melakukan absensi atau pengajuan hari ini.');
        }

        // --- LOGIC SINKRONISASI DASHBOARD ---
        $jamSekarang = date('H:i');
        $batasWaktu = '08:30';
        
        // Jika lewat 08:30, status utama menjadi 'Terlambat' agar indikator di dashboard berubah
        $statusFinal = ($jamSekarang > $batasWaktu) ? 'Terlambat' : 'Hadir';
        $keteranganSistem = ($jamSekarang > $batasWaktu) ? 'Terlambat Masuk' : 'Tepat Waktu';

        // Simpan Data Absensi
        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'user_id' => $user->id,
            'tanggal' => date('Y-m-d'),
            'jam_masuk' => date('H:i:s'),
            'status' => $statusFinal, // Menggunakan status Hadir atau Terlambat
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'keterangan' => $keteranganSistem 
        ]);

        return redirect()->route('dashboard')->with('success', 'Selamat bekerja! Berhasil absen masuk (' . $keteranganSistem . ').');
    }

    /**
     * Menangani Absen Pulang
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        // Memastikan absensi yang diupdate adalah milik user yang sedang login
        $absensi = Absensi::where('id', $id)
                          ->where('karyawan_id', $user->karyawan->id)
                          ->firstOrFail();
        
        // Update jam pulang dan ubah status menjadi Selesai
        $absensi->update([
            'jam_pulang' => date('H:i:s'),
            'status' => 'Selesai'
        ]);

        return back()->with('success', 'Berhasil absen pulang! Hati-hati di jalan.');
    }

    /**
     * Menangani Pengajuan Izin/Sakit (Backup jika dipanggil dari controller ini)
     */
    public function izinSakit(Request $request)
    {
        $user = Auth::user();

        if (!$user->karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $request->validate([
            'status' => 'required|in:Izin,Sakit',
            'keterangan' => 'required|string|max:255',
        ]);

        $sudahInput = Absensi::where('karyawan_id', $user->karyawan->id)
                            ->where('tanggal', date('Y-m-d'))
                            ->first();

        if ($sudahInput) {
            return back()->with('error', 'Gagal! Anda sudah mengisi kehadiran hari ini.');
        }

        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'user_id' => $user->id,
            'tanggal' => date('Y-m-d'),
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'jam_masuk' => date('H:i:s'), 
        ]);

        return redirect()->route('dashboard')->with('success', 'Pengajuan ' . $request->status . ' berhasil diproses.');
    }
}