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
        
        // Gunakan format string murni agar cocok dengan penyimpanan SQLite
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
        $absensis = collect();
        $cekAbsensi = null;

        // 1. LOGIKA UNTUK KARYAWAN (BAGIAN BAWAH DASHBOARD)
        if ($user->karyawan) {
            $karyawanId = $user->karyawan->id;
            
            // Cek status hari ini dengan perbandingan string langsung
            $cekAbsensi = Absensi::where('karyawan_id', $karyawanId)
                                ->where('tanggal', $hariIni)
                                ->first();

            // Total hadir karyawan selama ini
            $totalHadir = Absensi::where('karyawan_id', $karyawanId)
                                ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                                ->count();
        }

        // 2. LOGIKA KHUSUS ADMIN (CONTROL CENTER & MONITOR)
        if ($user->role == 'admin') {
            $totalKaryawan = Karyawan::count();

            // Ambil data yang TANGGAL-nya persis sama dengan hari ini
            $hadirHariIni = Absensi::where('tanggal', $hariIni)
                                    ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                                    ->count();

            $izinSakit = Absensi::where('tanggal', $hariIni)
                                ->whereIn('status', ['Izin', 'Sakit'])
                                ->count();

            // Isi tabel "MONITOR ABSENSI HARI INI"
            $recentActivities = Absensi::with(['karyawan'])
                                    ->where('tanggal', $hariIni)
                                    ->latest()
                                    ->get();

            // Isi tabel "REKAP KEHADIRAN KARYAWAN (MEI)"
            $rekapBulanan = Karyawan::select('id', 'nama_lengkap')
                ->withCount([
                    'absensi as total_hadir' => function ($query) use ($bulanIni, $tahunIni) {
                        $query->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                              ->where('tanggal', 'like', "$tahunIni-$bulanIni-%");
                    },
                    'absensi as total_izin' => function ($query) use ($bulanIni, $tahunIni) {
                        $query->where('status', 'Izin')
                              ->where('tanggal', 'like', "$tahunIni-$bulanIni-%");
                    },
                    'absensi as total_sakit' => function ($query) use ($bulanIni, $tahunIni) {
                        $query->where('status', 'Sakit')
                              ->where('tanggal', 'like', "$tahunIni-$bulanIni-%");
                    }
                ])
                ->get();
        }

        // Statistik Box Warna-warni (Filter berdasarkan bulan berjalan)
        $ringkasanStatistik = (object) [
            'total_hadir' => Absensi::where('tanggal', 'like', "$tahunIni-$bulanIni-%")->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])->count(),
            'total_izin'  => Absensi::where('tanggal', 'like', "$tahunIni-$bulanIni-%")->where('status', 'Izin')->count(),
            'total_sakit' => Absensi::where('tanggal', 'like', "$tahunIni-$bulanIni-%")->where('status', 'Sakit')->count(),
        ];

        return view('dashboard', compact(
            'totalHadir', 'totalKaryawan', 'hadirHariIni', 'izinSakit', 
            'recentActivities', 'rekapBulanan', 'ringkasanStatistik', 
            'absensis', 'namaBulan', 'cekAbsensi'
        ));
    }

    // LOGIKA PROTEKSI JARAK 20 METER SAAT KLIK TOMBOL ABSEN
    public function storeAbsen(Request $request)
    {
        $user = Auth::user();
        if (!$user->karyawan) return back()->with('error', 'Profil karyawan tidak ditemukan.');

        $sekarang = Carbon::now('Asia/Jakarta');
        $hariIni = $sekarang->toDateString();

        // 1. Koordinat Kantor PT Saltek Dumpang Jaya
        // GANTI angka ini dengan koordinat asli kantor kamu
        $latKantor = -3.4475; 
        $lngKantor = 114.8322; 

        // 2. Ambil koordinat dari GPS user
        $latUser = $request->latitude;
        $lngUser = $request->longitude;

        if (!$latUser || !$lngUser) {
            return back()->with('error', 'Lokasi tidak terdeteksi. Pastikan GPS aktif.');
        }

        // 3. Hitung Jarak (Haversine Formula)
        $earthRadius = 6371000; // dalam meter
        $dLat = deg2rad($latUser - $latKantor);
        $dLng = deg2rad($lngUser - $lngKantor);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latKantor)) * cos(deg2rad($latUser)) * sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $jarak = $earthRadius * $c;

        // 4. Validasi Jarak 20 Meter
        if ($jarak > 20) {
            return back()->with('error', 'Gagal Absen! Anda berada di luar radius kantor (' . round($jarak) . ' meter).');
        }

        // 5. Cek apakah sudah absen hari ini
        $sudahAbsen = Absensi::where('karyawan_id', $user->karyawan->id)
                            ->where('tanggal', $hariIni)
                            ->exists();

        if ($sudahAbsen) return back()->with('error', 'Anda sudah melakukan absensi hari ini.');

        // 6. Simpan Data Absensi
        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'user_id'     => $user->id,
            'tanggal'     => $hariIni,
            'jam_masuk'   => $sekarang->format('H:i:s'),
            'status'      => ($sekarang->format('H:i') > '08:00') ? 'Terlambat' : 'Hadir',
            'latitude'    => $latUser,
            'longitude'   => $lngUser
        ]);

        return back()->with('success', 'Berhasil Absen! Jarak: ' . round($jarak) . 'm');
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