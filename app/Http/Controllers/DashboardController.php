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
        $hariIni = Carbon::today();
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        
        $user = Auth::user();
        
        // 1. Inisialisasi variabel default agar tidak error di view
        $totalHadir = 0;
        $totalKaryawan = 0;
        $hadirHariIni = 0;
        $izinSakit = 0;
        $recentActivities = collect(); 
        $rekapBulanan = collect(); 
        $absensis = collect(); // Untuk daftar riwayat di halaman absensi karyawan

        // 2. Logika untuk User (Karyawan)
        if ($user->karyawan) {
            // Menghitung total hadir untuk statistik di dashboard user
            $totalHadir = Absensi::where('karyawan_id', $user->karyawan->id)
                                ->whereMonth('tanggal', $bulanIni)
                                ->whereYear('tanggal', $tahunIni)
                                ->whereIn('status', ['Hadir', 'Selesai'])
                                ->count();

            // Mengambil semua riwayat absensi milik user ini untuk ditampilkan di panel absensi
            $absensis = Absensi::where('karyawan_id', $user->karyawan->id)
                                ->latest()
                                ->get();
        }

        // 3. Logika Khusus Admin
        if ($user->role == 'admin') {
            // Total seluruh karyawan yang terdaftar
            $totalKaryawan = Karyawan::count();

            // Hitung yang hadir atau sudah pulang hari ini
            $hadirHariIni = Absensi::whereDate('tanggal', $hariIni)
                                    ->whereIn('status', ['Hadir', 'Selesai'])
                                    ->count();

            // Hitung yang izin atau sakit hari ini
            $izinSakit = Absensi::whereDate('tanggal', $hariIni)
                                ->whereIn('status', ['Izin', 'Sakit'])
                                ->count();

            // Ambil 5 aktivitas terbaru dengan relasi karyawan untuk tabel admin
            $recentActivities = Absensi::with('karyawan')
                                    ->whereDate('tanggal', $hariIni)
                                    ->latest()
                                    ->take(5)
                                    ->get();

            // REKAP BULANAN: Menghitung statistik setiap karyawan di bulan berjalan
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
                ])->get();
        }

        // Mengirimkan semua data ke view dashboard
        return view('dashboard', compact(
            'totalHadir', 
            'totalKaryawan', 
            'hadirHariIni', 
            'izinSakit', 
            'recentActivities',
            'rekapBulanan',
            'absensis'
        ));
    }
}