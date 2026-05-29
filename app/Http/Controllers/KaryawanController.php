<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KaryawanController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today()->format('Y-m-d');

        // --- LOGIKA STATISTIK UNTUK ADMIN ---
        
        // 1. Total seluruh anggota yang terdaftar
        $totalKaryawan = Karyawan::count();
        
        // 2. Jumlah yang sudah absen masuk atau selesai hari ini
        $hadirHariIni = Absensi::whereDate('tanggal', $hariIni)
                            ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                            ->count();
                            
        // 3. Jumlah yang mengirim laporan Izin atau Sakit hari ini
        $izinSakit = Absensi::whereDate('tanggal', $hariIni)
                            ->whereIn('status', ['Izin', 'Sakit'])
                            ->count();

        // Mengambil semua data karyawan dari database untuk tabel
        $karyawans = Karyawan::with('user')->latest()->get();

        // Mengirim semua data ke tampilan
        return view('karyawan.index', compact(
            'karyawans', 
            'totalKaryawan', 
            'hadirHariIni', 
            'izinSakit'
        ));

   public function create()
{
    // Mengambil user yang ID-nya belum ada di tabel karyawans
    $karyawanUserIds = Karyawan::pluck('user_id')->toArray();
    $users = User::whereNotIn('id', $karyawanUserIds)->get();
    
    return view('karyawan.create', compact('users'));
}


    // Fungsi untuk menyimpan data karyawan baru
    public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|unique:karyawans,user_id',
        'nama_lengkap' => 'required',
        'jabatan' => 'required',
        'nip' => 'required|unique:karyawans,nip',
        'no_hp' => 'required|unique:karyawans,no_hp', // Tambahkan unique di sini
    ], [
        'user_id.unique' => 'Akun ini sudah terdaftar sebagai karyawan.',
        'nip.unique' => 'NIP ini sudah digunakan oleh karyawan lain.',
        'no_hp.unique' => 'Nomor WhatsApp ini sudah terdaftar di sistem.',
    ]);

    Karyawan::create($request->all());

    return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan!');
}

    // --- BAGIAN EDIT, UPDATE, DESTROY ---

    // Fungsi untuk menampilkan form edit
    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $users = User::all(); 
        return view('karyawan.edit', compact('karyawan', 'users'));
    }

    // Fungsi untuk menyimpan perubahan data
   public function update(Request $request, $id)
{
    $karyawan = Karyawan::findOrFail($id);

    $request->validate([
        'user_id' => 'required|unique:karyawans,user_id,' . $id,
        'nama_lengkap' => 'required',
        'jabatan' => 'required',
        'nip' => 'required|unique:karyawans,nip,' . $id,
        'no_hp' => 'required|unique:karyawans,no_hp,' . $id, // Tambahkan unique dengan id
    ]);

    $karyawan->update($request->all());

    return redirect()->route('karyawan.index')->with('success', 'Data berhasil diperbarui!');
}
    // Fungsi untuk menghapus data
    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan telah dihapus dari sistem.');
    }

    /**
     * Fungsi Baru: Pimpinan Index
     * Untuk menampilkan monitor kehadiran khusus Boss/Pimpinan
     */
    public function pimpinanIndex()
    {
        $hariIni = Carbon::now('Asia/Jakarta')->toDateString();
        $bulanIni = Carbon::now('Asia/Jakarta')->month;
        $tahunIni = Carbon::now('Asia/Jakarta')->year;

        // Ambil data absensi hari ini beserta data karyawannya
        $absensiHariIni = Absensi::with('karyawan')
                            ->whereDate('tanggal', $hariIni)
                            ->get();

        // Hitung statistik singkat untuk pimpinan
        $totalHadir = $absensiHariIni->whereIn('status', ['Hadir', 'Terlambat', 'Selesai'])->count();
        $totalIzin = $absensiHariIni->where('status', 'Izin')->count();
        $totalSakit = $absensiHariIni->where('status', 'Sakit')->count();

        // Tambahan: Mengambil data rekapitulasi bulanan kumulatif seluruh karyawan untuk pimpinan
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

        return view('pimpinan.index', compact('absensiHariIni', 'totalHadir', 'totalIzin', 'totalSakit', 'rekapBulanan'));
    }
}