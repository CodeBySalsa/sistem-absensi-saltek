<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Set locale ke Bahasa Indonesia agar nama bulan otomatis Indonesia
        Carbon::setLocale('id');
        
        $sekarang = Carbon::now();
        $hariIni = $sekarang->toDateString();
        $bulanIni = $sekarang->month;
        $tahunIni = $sekarang->year;
        $namaBulan = $sekarang->translatedFormat('F'); 
        
        $user = Auth::user();
        
        // 1. Inisialisasi variabel default agar tidak error di view
        $totalHadir = 0;
        $totalKaryawan = 0;
        $hadirHariIni = 0;
        $izinSakit = 0;
        $recentActivities = collect(); 
        $rekapBulanan = collect(); 
        $absensis = collect(); 
        $cekAbsensi = null; // Tambahan variabel untuk pengecekan di Blade

        // 2. Logika untuk User (Karyawan)
        if ($user->karyawan) {
            // [OPTIMALISASI] Ambil status absen user hari ini untuk menggantikan query di Blade
            $cekAbsensi = Absensi::where('karyawan_id', $user->karyawan->id)
                                ->whereDate('tanggal', $hariIni)
                                ->first();

            $totalHadir = Absensi::where('karyawan_id', $user->karyawan->id)
                                ->whereMonth('tanggal', $bulanIni)
                                ->whereYear('tanggal', $tahunIni)
                                ->whereIn('status', ['Hadir', 'Selesai'])
                                ->count();

            // Ambil 10 riwayat terbaru milik user tersebut
            $absensis = Absensi::where('karyawan_id', $user->karyawan->id)
                                ->latest('tanggal')
                                ->take(10) 
                                ->get();
        }

        // 3. Logika Khusus Admin
        if ($user->role == 'admin') {
            $totalKaryawan = Karyawan::count();

            $hadirHariIni = Absensi::whereDate('tanggal', $hariIni)
                                    ->whereIn('status', ['Hadir', 'Selesai'])
                                    ->count();

            $izinSakit = Absensi::whereDate('tanggal', $hariIni)
                                ->whereIn('status', ['Izin', 'Sakit'])
                                ->count();

            // Mengambil 5 aktivitas terbaru hari ini untuk tabel log
            $recentActivities = Absensi::with('karyawan')
                                    ->whereDate('tanggal', $hariIni)
                                    ->latest()
                                    ->take(5)
                                    ->get();

            // REKAPITULASI STATISTIK
            $rekapBulanan = Karyawan::withCount([
                    'absensis as total_hadir' => function ($query) use ($bulanIni, $tahunIni) {
                        $query->whereIn('status', ['Hadir', 'Selesai'])
                              ->whereMonth('tanggal', $bulanIni)
                              ->whereYear('tanggal', $tahunIni);
                    },
                    'absensis as total_izin' => function ($query) use ($bulanIni, $tahunIni) {
                        $query->where('status', 'Izin')
                              ->whereMonth('tanggal', $bulanIni)
                              ->whereYear('tanggal', $tahunIni);
                    },
                    'absensis as total_sakit' => function ($query) use ($bulanIni, $tahunIni) {
                        $query->where('status', 'Sakit')
                              ->whereMonth('tanggal', $bulanIni)
                              ->whereYear('tanggal', $tahunIni);
                    }
                ])
                ->get()
                ->filter(function ($karyawan) {
                    return ($karyawan->total_hadir + $karyawan->total_izin + $karyawan->total_sakit) > 0;
                });
        }

        // Mengirimkan data ke view dashboard
        return view('dashboard', compact(
            'totalHadir', 
            'totalKaryawan', 
            'hadirHariIni', 
            'izinSakit', 
            'recentActivities',
            'rekapBulanan',
            'absensis',
            'namaBulan',
            'cekAbsensi' // Kirim variabel cekAbsensi ke View
        ));
    }

    /**
     * Menangani pengajuan Izin atau Sakit dari Dashboard
     */
    public function izinSakit(Request $request)
    {
        $request->validate([
            'status' => 'required|in:Izin,Sakit',
            'keterangan' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        if (!$user->karyawan) {
            return back()->with('error', 'Profil karyawan Anda tidak ditemukan.');
        }

        $sudahAbsen = Absensi::where('karyawan_id', $user->karyawan->id)
                             ->whereDate('tanggal', Carbon::today())
                             ->exists();

        if ($sudahAbsen) {
            return back()->with('error', 'Anda sudah melakukan absensi atau pengajuan hari ini.');
        }

        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'tanggal' => Carbon::today(),
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'jam_masuk' => null, 
        ]);

        return back()->with('success', 'Berhasil mengirimkan status ' . $request->status);
    }
}