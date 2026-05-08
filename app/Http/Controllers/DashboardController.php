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
        
        $hariIni = $sekarang->toDateString(); 
        $bulanIni = $sekarang->format('m');
        $tahunIni = $sekarang->year;
        $namaBulan = $sekarang->translatedFormat('F'); 
        
        $user = Auth::user();
        
        // Inisialisasi variabel awal
        $totalHadir = 0; 
        $totalKaryawan = 0; 
        $hadirHariIni = 0; 
        $izinSakit = 0;
        $recentActivities = collect(); 
        $rekapBulanan = collect(); 
        $cekAbsensi = null;

        // 1. LOGIKA UNTUK KARYAWAN (BAGIAN ATAS DASHBOARD)
        if ($user->karyawan) {
            $karyawanId = $user->karyawan->id;
            
            $cekAbsensi = Absensi::where('karyawan_id', $karyawanId)
                                ->where('tanggal', $hariIni)
                                ->first();

            $totalHadir = Absensi::where('karyawan_id', $karyawanId)
                                ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                                ->whereMonth('tanggal', $bulanIni)
                                ->whereYear('tanggal', $tahunIni)
                                ->count();
        }

        // 2. LOGIKA KHUSUS ADMIN (CONTROL CENTER & MONITOR)
        if ($user->role == 'admin') {
            $totalKaryawan = Karyawan::count();

            $hadirHariIni = Absensi::where('tanggal', $hariIni)
                                    ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                                    ->count();

            $izinSakit = Absensi::where('tanggal', $hariIni)
                                ->whereIn('status', ['Izin', 'Sakit'])
                                ->count();

            $recentActivities = Absensi::with(['karyawan'])
                                    ->where('tanggal', $hariIni)
                                    ->latest()
                                    ->get();

            // PERBAIKAN: Menghitung rekap bulanan untuk tabel admin
            $rekapBulanan = Karyawan::withCount([
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
            ])->get();
        }

        // Statistik Ringkasan (Box Warna-warni)
        $ringkasanStatistik = (object) [
            'total_hadir' => Absensi::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])->count(),
            'total_izin'  => Absensi::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->where('status', 'Izin')->count(),
            'total_sakit' => Absensi::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->where('status', 'Sakit')->count(),
        ];

        return view('dashboard', compact(
            'totalHadir', 'totalKaryawan', 'hadirHariIni', 'izinSakit', 
            'recentActivities', 'rekapBulanan', 'ringkasanStatistik', 
            'namaBulan', 'cekAbsensi'
        ));
    }

    public function storeAbsen(Request $request)
    {
        $user = Auth::user();
        if (!$user->karyawan) return back()->with('error', 'Profil karyawan tidak ditemukan.');

        $sekarang = Carbon::now('Asia/Jakarta');
        $hariIni = $sekarang->toDateString();
        $jamSekarang = $sekarang->format('H:i:s');

        $absensi = Absensi::where('karyawan_id', $user->karyawan->id)
                            ->where('tanggal', $hariIni)
                            ->first();

        // LOGIKA ABSEN PULANG
        if ($absensi) {
            if ($absensi->jam_keluar) {
                return back()->with('error', 'Anda sudah melakukan absensi masuk dan pulang hari ini.');
            }

            if ($sekarang->hour < 17) {
                return back()->with('error', 'Belum waktunya pulang! Absen pulang baru bisa dilakukan mulai pukul 17:00.');
            }

            $absensi->update([
                'jam_keluar' => $jamSekarang,
                'status'     => 'Selesai' 
            ]);

            return redirect()->route('dashboard')->with('success', 'Berhasil Absen Pulang! Hati-hati di jalan.');
        }

        // LOGIKA ABSEN MASUK
        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'user_id'     => $user->id,
            'tanggal'     => $hariIni,
            'jam_masuk'   => $jamSekarang,
            'status'      => ($sekarang->hour >= 8 && $sekarang->minute > 0) ? 'Terlambat' : 'Hadir',
            'latitude'    => $request->lat ?? 0,
            'longitude'   => $request->lng ?? 0,
        ]);

        return redirect()->route('dashboard')->with('success', 'Berhasil Absen Masuk! Selamat bekerja.');
    }

    public function izinSakit(Request $request)
    {
        $request->validate([
            'status' => 'required|in:Izin,Sakit',
            'keterangan' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        if (!$user->karyawan) return back()->with('error', 'Profil karyawan tidak ditemukan.');

        $hariIni = Carbon::now('Asia/Jakarta')->toDateString();
        $sudahAbsen = Absensi::where('karyawan_id', $user->karyawan->id)
                            ->where('tanggal', $hariIni)
                            ->exists();

        if ($sudahAbsen) return back()->with('error', 'Anda sudah melakukan absensi/izin hari ini.');

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