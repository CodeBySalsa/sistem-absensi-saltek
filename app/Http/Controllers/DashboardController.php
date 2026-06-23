<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // LOGIKA BARU: Jika yang login adalah Pimpinan, langsung lempar ke halaman rekap
        if ($user->role == 'pimpinan') {
            return redirect()->route('pimpinan.index');
        }

        $hariIni = Carbon::now('Asia/Jakarta')->toDateString();
        $tahunIni = Carbon::now('Asia/Jakarta')->year;

        // Ambil bulan & tahun dari dropdown, default bulan ini
        $bulanDipilih = $request->input('bulan', Carbon::now('Asia/Jakarta')->month);
        $tahunDipilih = $request->input('tahun', $tahunIni);

        $namaBulan = Carbon::createFromDate($tahunDipilih, $bulanDipilih, 1)->translatedFormat('F');

        // Inisialisasi variabel default agar tidak error di view
        $absensis = collect();
        $totalKaryawan = 0;
        $hadirHariIni = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $absensiHariIni = collect();
        $recentActivities = collect();
        $rekapBulanan = collect();
        $cekAbsensi = null;
        $totalHadir = 0;

        // 1. LOGIKA MINGGU INI (Hanya untuk Karyawan)
        if ($user->karyawan) {
            // Ambil range minggu ini (Senin - Sabtu)
            $startOfWeek = Carbon::now('Asia/Jakarta')->startOfWeek();
            $endOfWeek = Carbon::now('Asia/Jakarta')->startOfWeek()->addDays(5);

            $queryAbsensi = Absensi::where('karyawan_id', $user->karyawan->id)
                            ->whereBetween('tanggal', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                            ->oldest()
                            ->get();

            // Logika Reset: Jika sudah 6 hari, maka kosongkan ($absensis jadi collect kosong)
            if ($queryAbsensi->count() >= 6) {
                $absensis = collect();
            } else {
                $absensis = $queryAbsensi;
            }

            $cekAbsensi = Absensi::where('karyawan_id', $user->karyawan->id)
                                 ->where('tanggal', $hariIni)
                                 ->first();

            $totalHadir = Absensi::where('karyawan_id', $user->karyawan->id)
                                 ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                                 ->whereMonth('tanggal', $bulanDipilih)
                                 ->whereYear('tanggal', $tahunDipilih)
                                 ->count();
        }

        // 2. LOGIKA KHUSUS ADMIN (CONTROL CENTER & MONITOR)
        if ($user->role == 'admin') {
            $totalKaryawan = Karyawan::count();

            $hadirHariIni = Absensi::where('tanggal', $hariIni)
                                    ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                                    ->count();

            $totalIzin = Absensi::where('tanggal', $hariIni)
                                ->where('status', 'Izin')
                                ->count();

            $totalSakit = Absensi::where('tanggal', $hariIni)
                                 ->where('status', 'Sakit')
                                 ->count();

            // DATA MONITORING HARIAN ADMIN (Siapa yang absen hari ini)
            $absensiHariIni = Absensi::with(['user', 'karyawan'])
                                    ->where('tanggal', $hariIni)
                                    ->latest()
                                    ->get();

            $recentActivities = Absensi::with(['karyawan'])
                                    ->where('tanggal', $hariIni)
                                    ->latest()
                                    ->get();

            // REKAPITULASI BULANAN — sesuai bulan & tahun yang dipilih dropdown
            $rekapBulanan = Karyawan::withCount([
                'absensi as total_hadir' => function ($query) use ($bulanDipilih, $tahunDipilih) {
                    $query->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                          ->whereMonth('tanggal', $bulanDipilih)
                          ->whereYear('tanggal', $tahunDipilih);
                },
                'absensi as total_izin' => function ($query) use ($bulanDipilih, $tahunDipilih) {
                    $query->where('status', 'Izin')
                          ->whereMonth('tanggal', $bulanDipilih)
                          ->whereYear('tanggal', $tahunDipilih);
                },
                'absensi as total_sakit' => function ($query) use ($bulanDipilih, $tahunDipilih) {
                    $query->where('status', 'Sakit')
                          ->whereMonth('tanggal', $bulanDipilih)
                          ->whereYear('tanggal', $tahunDipilih);
                }
            ])->get();
        }

        // Statistik Ringkasan Pribadi (Box di Banner Biru Karyawan)
        $bulanIni = Carbon::now('Asia/Jakarta')->month;
        $ringkasanStatistik = (object) [
            'total_hadir' => Absensi::where('karyawan_id', $user->karyawan->id ?? 0)
                                    ->whereMonth('tanggal', $bulanIni)
                                    ->whereYear('tanggal', $tahunIni)
                                    ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                                    ->count(),
            'total_izin'  => Absensi::where('karyawan_id', $user->karyawan->id ?? 0)
                                    ->whereMonth('tanggal', $bulanIni)
                                    ->whereYear('tanggal', $tahunIni)
                                    ->where('status', 'Izin')
                                    ->count(),
            'total_sakit' => Absensi::where('karyawan_id', $user->karyawan->id ?? 0)
                                    ->whereMonth('tanggal', $bulanIni)
                                    ->whereYear('tanggal', $tahunIni)
                                    ->where('status', 'Sakit')
                                    ->count(),
        ];

        return view('dashboard', compact(
            'absensis', 'totalHadir', 'totalKaryawan', 'hadirHariIni', 'totalIzin', 'totalSakit',
            'recentActivities', 'rekapBulanan', 'ringkasanStatistik', 'absensiHariIni',
            'namaBulan', 'cekAbsensi', 'bulanDipilih', 'tahunDipilih'
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

        // LOGIKA PERBAIKAN: Deteksi Terlambat jika sudah lewat jam 08:00 pagi
        $statusAbsen = ($sekarang->hour >= 8 && $sekarang->minute > 0) ? 'Terlambat' : 'Hadir';

        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'user_id'     => $user->id,
            'tanggal'     => $hariIni,
            'jam_masuk'   => $jamSekarang,
            'status'      => $statusAbsen,
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