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
        Carbon::setLocale('id');
        $sekarang = Carbon::now('Asia/Jakarta');
        
        // Penting: SQLite terkadang butuh format Y-m-d yang tegas
        $hariIni = $sekarang->toDateString(); 
        $bulanIni = $sekarang->month;
        $tahunIni = $sekarang->year;
        $namaBulan = $sekarang->translatedFormat('F'); 
        
        $user = Auth::user();
        
        // Inisialisasi variabel agar view tidak error
        $totalHadir = 0; $totalKaryawan = 0; $hadirHariIni = 0; $izinSakit = 0;
        $recentActivities = collect(); $rekapBulanan = collect(); $absensis = collect();
        $cekAbsensi = null;

        // 1. Logika untuk Karyawan (Bagian Bawah Dashboard)
        if ($user->karyawan) {
            $karyawanId = $user->karyawan->id;
            
            $cekAbsensi = Absensi::where('karyawan_id', $karyawanId)
                                ->whereDate('tanggal', $hariIni)
                                ->first();

            $totalHadir = Absensi::where('karyawan_id', $karyawanId)
                                ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                                ->count();
        }

        // 2. Logika untuk Admin (Control Center & Monitor)
        if ($user->role == 'admin') {
            $totalKaryawan = Karyawan::count();

            // Hitung Hadir Hari Ini (Hanya tanggal hari ini)
            $hadirHariIni = Absensi::whereDate('tanggal', $hariIni)
                                    ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                                    ->count();

            // Hitung Izin/Sakit Hari Ini
            $izinSakit = Absensi::whereDate('tanggal', $hariIni)
                                ->whereIn('status', ['Izin', 'Sakit'])
                                ->count();

            // Isi tabel "MONITOR ABSENSI HARI INI"
            $recentActivities = Absensi::with(['karyawan'])
                                    ->whereDate('tanggal', $hariIni)
                                    ->latest()
                                    ->get();

            // Isi tabel "REKAP KEHADIRAN KARYAWAN (MEI)"
            $rekapBulanan = Karyawan::select('id', 'nama_lengkap') 
                ->withCount([
                    'absensi as total_hadir' => function ($query) use ($bulanIni, $tahunIni) {
                        $query->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
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

        // Ringkasan Statistik untuk Box Atas
        $ringkasanStatistik = (object) [
            'total_hadir' => Absensi::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])->count(),
            'total_izin'  => Absensi::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->where('status', 'Izin')->count(),
            'total_sakit' => Absensi::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->where('status', 'Sakit')->count(),
        ];

        return view('dashboard', compact(
            'totalHadir', 'totalKaryawan', 'hadirHariIni', 'izinSakit', 
            'recentActivities', 'rekapBulanan', 'ringkasanStatistik', 
            'absensis', 'namaBulan', 'cekAbsensi'
        ));
    }

    public function izinSakit(Request $request)
    {
        $request->validate([
            'status' => 'required|in:Izin,Sakit',
            'keterangan' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $hariIni = Carbon::now('Asia/Jakarta')->toDateString();

        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'user_id'     => $user->id, 
            'tanggal'     => $hariIni,
            'status'      => $request->status,
            'keterangan'  => $request->keterangan,
            'jam_masuk'   => Carbon::now('Asia/Jakarta')->format('H:i:s'), 
        ]);

        return back()->with('success', 'Berhasil mengirimkan status ' . $request->status);
    }
}