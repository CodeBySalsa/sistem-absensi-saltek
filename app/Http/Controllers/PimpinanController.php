<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;

class PimpinanController extends Controller
{
    public function index()
    {
        // 1. Ambil data absensi hari ini
        $absensiHariIni = Absensi::whereDate('tanggal', Carbon::today())->get();

        // 2. Hitung statistik
        $totalHadir = $absensiHariIni->whereIn('status', ['Hadir', 'Terlambat', 'Selesai'])->count();
        $totalIzin = $absensiHariIni->where('status', 'Izin')->count();
        $totalSakit = $absensiHariIni->where('status', 'Sakit')->count();

        // 3. Ambil data untuk rekap bulanan (Misal dari model Karyawan)
        $rekapBulanan = Karyawan::with(['absensi' => function($query) {
            $query->whereMonth('tanggal', Carbon::now()->month);
        }])->get()->map(function($karyawan) {
            return (object) [
                'nama_lengkap' => $karyawan->nama_lengkap,
                'total_hadir' => $karyawan->absensi->whereIn('status', ['Hadir', 'Terlambat', 'Selesai'])->count(),
                'total_izin' => $karyawan->absensi->where('status', 'Izin')->count(),
                'total_sakit' => $karyawan->absensi->where('status', 'Sakit')->count(),
            ];
        });

        // 4. Hitung total karyawan yang terdaftar
        $totalKaryawan = Karyawan::count();

        return view('pimpinan.index', compact(
            'absensiHariIni', 
            'rekapBulanan', 
            'totalHadir', 
            'totalIzin', 
            'totalSakit', 
            'totalKaryawan'
        ));
    }
}