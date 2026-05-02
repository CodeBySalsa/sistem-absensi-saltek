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
        // Set locale ke Bahasa Indonesia
        Carbon::setLocale('id');
        
        $sekarang = Carbon::now();
        $hariIni = $sekarang->toDateString();
        $bulanIni = $sekarang->month;
        $tahunIni = $sekarang->year;
        $namaBulan = $sekarang->translatedFormat('F'); 
        
        $user = Auth::user();
        
        // 1. Inisialisasi variabel default
        $totalHadir = 0;
        $totalKaryawan = 0;
        $hadirHariIni = 0;
        $izinSakit = 0;
        $recentActivities = collect(); 
        $rekapBulanan = collect(); 
        $absensis = collect(); 
        $cekAbsensi = null;

        // 2. Logika untuk User (Karyawan)
        if ($user->karyawan) {
            $karyawanId = $user->karyawan->id;

            // Ambil status absen user hari ini
            $cekAbsensi = Absensi::where('karyawan_id', $karyawanId)
                                ->whereDate('tanggal', $hariIni)
                                ->first();

            // Total hadir bulan ini
            $totalHadir = Absensi::where('karyawan_id', $karyawanId)
                                ->whereMonth('tanggal', $bulanIni)
                                ->whereYear('tanggal', $tahunIni)
                                ->whereIn('status', ['Hadir', 'Selesai'])
                                ->count();

            // 10 riwayat terbaru milik user
            $absensis = Absensi::where('karyawan_id', $karyawanId)
                                ->latest('tanggal')
                                ->take(10) 
                                ->get();
        }

        // 3. Logika Khusus Admin
        if ($user->role == 'admin') {
            $totalKaryawan = Karyawan::count();

            // Statistik Hari Ini
            $hadirHariIni = Absensi::whereDate('tanggal', $hariIni)
                                    ->whereIn('status', ['Hadir', 'Selesai'])
                                    ->count();

            $izinSakit = Absensi::whereDate('tanggal', $hariIni)
                                ->whereIn('status', ['Izin', 'Sakit'])
                                ->count();

            // 5 aktivitas terbaru hari ini - Memuat relasi 'karyawan'
            $recentActivities = Absensi::with('karyawan')
                                    ->whereDate('tanggal', $hariIni)
                                    ->latest()
                                    ->take(5)
                                    ->get();

            // REKAPITULASI STATISTIK (Menampilkan semua karyawan)
            // PERBAIKAN: Mengambil 'nama_lengkap' dan memuat relasi 'karyawan' agar konsisten dengan Blade
            $rekapBulanan = Karyawan::select('id', 'nama_lengkap') 
                ->withCount([
                    'absensi as total_hadir' => function ($query) use ($bulanIni, $tahunIni) {
                        $query->whereIn('status', ['Hadir', 'Selesai'])
                              ->whereMonth('tanggal', $bulanIni)
                              ->whereYear('tanggal', $tahunIni);
                    },
                    'absensi as total_izin' => function ($query) use ($bulanIni, $tahunIni) {
                        $query->where('status', 'Izin')
                              ->whereMonth('tanggal', $bulanIni)
                              ->whereYear('tanggal', $tahunIni);
                    },
                    'absensi as total_sakit' => function ($query) use ($bulanIni, $tahunIni) {
                        $query->where('status', 'Sakit')
                              ->whereMonth('tanggal', $bulanIni)
                              ->whereYear('tanggal', $tahunIni);
                    }
                ])
                ->get(); 
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
            'cekAbsensi'
        ));
    }

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
            'user_id' => $user->id, 
            'tanggal' => Carbon::today(),
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'jam_masuk' => now()->format('H:i:s'), 
        ]);

        return back()->with('success', 'Berhasil mengirimkan status ' . $request->status);
    }
}