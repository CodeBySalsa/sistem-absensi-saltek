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
        
        $sekarang = Carbon::now();
        $hariIni = $sekarang->toDateString();
        $bulanIni = $sekarang->month;
        $tahunIni = $sekarang->year;
        $namaBulan = $sekarang->translatedFormat('F'); 
        
        $user = Auth::user();
        
        $totalHadir = 0;
        $totalKaryawan = 0;
        $hadirHariIni = 0;
        $izinSakit = 0;
        $recentActivities = collect(); 
        $rekapBulanan = collect(); 
        $ringkasanStatistik = null; 
        $absensis = collect(); 
        $cekAbsensi = null;

        if ($user->karyawan) {
            $karyawanId = $user->karyawan->id;
            $cekAbsensi = Absensi::where('karyawan_id', $karyawanId)
                                ->whereDate('tanggal', $hariIni)
                                ->first();

            $totalHadir = Absensi::where('karyawan_id', $karyawanId)
                                ->whereMonth('tanggal', $bulanIni)
                                ->whereYear('tanggal', $tahunIni)
                                ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                                ->count();

            $recentActivities = Absensi::where('karyawan_id', $karyawanId)
                                ->where('tanggal', '>=', Carbon::now()->subDays(7)) 
                                ->latest('tanggal')
                                ->get();
        }

        if ($user->role == 'admin') {
            $totalKaryawan = Karyawan::count();
            $hadirHariIni = Absensi::whereDate('tanggal', $hariIni)
                                    ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                                    ->count();
            $izinSakit = Absensi::whereDate('tanggal', $hariIni)
                                ->whereIn('status', ['Izin', 'Sakit'])
                                ->count();

            $recentActivities = Absensi::with(['karyawan'])
                                    ->whereDate('tanggal', $hariIni)
                                    ->latest()
                                    ->get();

            $ringkasanStatistik = (object) [
                'total_hadir' => Absensi::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])->count(),
                'total_izin'  => Absensi::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->where('status', 'Izin')->count(),
                'total_sakit' => Absensi::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->where('status', 'Sakit')->count(),
            ];

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

        return view('dashboard', compact(
            'totalHadir', 
            'totalKaryawan', 
            'hadirHariIni', 
            'izinSakit', 
            'recentActivities',
            'rekapBulanan',
            'ringkasanStatistik',
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
        if (!$user->karyawan) return back()->with('error', 'Profil tidak ditemukan.');

        $sudahAbsen = Absensi::where('karyawan_id', $user->karyawan->id)->whereDate('tanggal', Carbon::today())->exists();
        if ($sudahAbsen) return back()->with('error', 'Anda sudah absen hari ini.');

        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'user_id'     => $user->id, 
            'tanggal'     => Carbon::today(),
            'status'      => $request->status,
            'keterangan'  => $request->keterangan,
            'jam_masuk'   => now()->format('H:i:s'), 
        ]);

        return back()->with('success', 'Berhasil mengirimkan status ' . $request->status);
    }
}