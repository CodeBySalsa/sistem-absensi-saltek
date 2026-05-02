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

        // Tambahan Validasi: Memastikan data GPS terkirim dari modal
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ], [
            'latitude.required' => 'Lokasi GPS belum terdeteksi.',
            'longitude.required' => 'Lokasi GPS belum terdeteksi.',
        ]);

        // Cek apakah hari ini sudah ada data (Hadir/Izin/Sakit)
        $sudahInput = Absensi::where('karyawan_id', $user->karyawan->id)
                            ->where('tanggal', date('Y-m-d'))
                            ->first();

        if ($sudahInput) {
            return back()->with('error', 'Anda sudah melakukan absensi atau pengajuan hari ini.');
        }

        // LOGIC: Cek Keterlambatan (Batas 08:30)
        $jamSekarang = date('H:i');
        $batasWaktu = '08:30';
        $statusKeterangan = ($jamSekarang > $batasWaktu) ? 'Terlambat Masuk' : 'Tepat Waktu';

        // Simpan Data Absensi termasuk koordinat dari Modal
        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'tanggal' => date('Y-m-d'),
            'jam_masuk' => date('H:i:s'),
            'status' => 'Hadir',
            'latitude' => $request->latitude,   // Menangkap data dari modal
            'longitude' => $request->longitude, // Menangkap data dari modal
            'keterangan' => $statusKeterangan 
        ]);

        return redirect()->route('dashboard')->with('success', 'Selamat bekerja! Berhasil absen masuk (' . $statusKeterangan . ').');
    }

    /**
     * Menangani Absen Pulang
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        // Tambahan Keamanan: Memastikan absensi yang diupdate adalah milik user yang sedang login
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
     * Menangani Pengajuan Izin/Sakit
     */
    public function izinSakit(Request $request)
    {
        $user = Auth::user();

        if (!$user->karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Validasi input
        $request->validate([
            'status' => 'required|in:Izin,Sakit',
            'keterangan' => 'required|string|max:255',
        ]);

        // Cek apakah hari ini sudah ada data
        $sudahInput = Absensi::where('karyawan_id', $user->karyawan->id)
                            ->where('tanggal', date('Y-m-d'))
                            ->first();

        if ($sudahInput) {
            return back()->with('error', 'Gagal! Anda sudah mengisi kehadiran hari ini.');
        }

        // Simpan data pengajuan
        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'tanggal' => date('Y-m-d'),
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'jam_masuk' => date('H:i:s'), 
        ]);

        return redirect()->route('dashboard')->with('success', 'Pengajuan ' . $request->status . ' berhasil diproses.');
    }
}