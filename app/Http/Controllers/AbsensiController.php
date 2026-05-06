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
     * Koordinat Kantor PT Saltek Dumpang Jaya
     * Silakan ganti angka ini dengan titik koordinat kantor yang sebenarnya
     */
    private $officeLat = -3.315000; // Ganti dengan latitude kantor asli
    private $officeLng = 114.590000; // Ganti dengan longitude kantor asli
    private $radiusLimit = 20; // Batas radius dalam meter

    /**
     * Menampilkan halaman riwayat absensi
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role == 'admin') {
            $absensis = Absensi::with('karyawan')->latest()->get();
        } else {
            $absensis = Absensi::where('karyawan_id', $user->karyawan->id ?? 0)
                                ->latest()
                                ->get();
        }

        return view('absensi.index', compact('absensis'));
    }

    /**
     * Fungsi Hitung Jarak (Haversine Formula)
     * Untuk memastikan user berada dalam radius yang ditentukan
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    /**
     * Menangani Absen Masuk (Hadir)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ], [
            'latitude.required' => 'Lokasi GPS belum terdeteksi.',
            'longitude.required' => 'Lokasi GPS belum terdeteksi.',
        ]);

        // Proteksi Jarak Radius 20 Meter
        $distance = $this->calculateDistance($request->latitude, $request->longitude, $this->officeLat, $this->officeLng);
        
        if ($distance > $this->radiusLimit) {
            return back()->with('error', 'Gagal! Jarak Anda ' . round($distance) . 'm dari kantor. Maksimal radius ' . $this->radiusLimit . 'm.');
        }

        $sudahInput = Absensi::where('karyawan_id', $user->karyawan->id)
                            ->where('tanggal', date('Y-m-d'))
                            ->first();

        if ($sudahInput) {
            return back()->with('error', 'Anda sudah melakukan absensi atau pengajuan hari ini.');
        }

        $jamSekarang = date('H:i');
        $batasWaktu = '08:30';
        
        $statusFinal = ($jamSekarang > $batasWaktu) ? 'Terlambat' : 'Hadir';
        $keteranganSistem = ($jamSekarang > $batasWaktu) ? 'Terlambat Masuk' : 'Tepat Waktu';

        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'user_id' => $user->id,
            'tanggal' => date('Y-m-d'),
            'jam_masuk' => date('H:i:s'),
            'status' => $statusFinal, 
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'keterangan' => $keteranganSistem 
        ]);

        return redirect()->route('dashboard')->with('success', 'Selamat bekerja! Berhasil absen masuk (' . $keteranganSistem . ').');
    }

    /**
     * Menangani Absen Pulang
     */
    public function pulang(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ], [
            'latitude.required' => 'Lokasi GPS pulang belum terdeteksi.',
            'longitude.required' => 'Lokasi GPS pulang belum terdeteksi.',
        ]);

        // Proteksi Jarak Radius 20 Meter saat Pulang
        $distance = $this->calculateDistance($request->latitude, $request->longitude, $this->officeLat, $this->officeLng);
        
        if ($distance > $this->radiusLimit) {
            return back()->with('error', 'Gagal! Anda harus berada di area kantor untuk melakukan absen pulang.');
        }

        $absensi = Absensi::where('karyawan_id', $user->karyawan->id)
                          ->where('tanggal', date('Y-m-d'))
                          ->first();

        if (!$absensi) {
            return redirect()->back()->with('error', 'Data absensi masuk tidak ditemukan.');
        }

        if ($absensi->jam_pulang) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        $absensi->update([
            'jam_pulang' => date('H:i:s'),
            'status' => 'Selesai',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('dashboard')->with('success', 'Berhasil absen pulang! Hati-hati di jalan.');
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

        $request->validate([
            'status' => 'required|in:Izin,Sakit',
            'keterangan' => 'required|string|max:255',
        ]);

        $sudahInput = Absensi::where('karyawan_id', $user->karyawan->id)
                            ->where('tanggal', date('Y-m-d'))
                            ->first();

        if ($sudahInput) {
            return back()->with('error', 'Gagal! Anda sudah mengisi kehadiran hari ini.');
        }

        Absensi::create([
            'karyawan_id' => $user->karyawan->id,
            'user_id' => $user->id,
            'tanggal' => date('Y-m-d'),
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'jam_masuk' => date('H:i:s'), 
        ]);

        return redirect()->route('dashboard')->with('success', 'Pengajuan ' . $request->status . ' berhasil diproses.');
    }
}