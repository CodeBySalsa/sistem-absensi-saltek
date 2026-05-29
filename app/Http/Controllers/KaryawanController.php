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

        $totalKaryawan = Karyawan::count();
        
        $hadirHariIni = Absensi::whereDate('tanggal', $hariIni)
                            ->whereIn('status', ['Hadir', 'Selesai', 'Terlambat'])
                            ->count();
                            
        $izinSakit = Absensi::whereDate('tanggal', $hariIni)
                            ->whereIn('status', ['Izin', 'Sakit'])
                            ->count();

        $karyawans = Karyawan::with('user')->latest()->get();

        return view('karyawan.index', compact(
            'karyawans', 
            'totalKaryawan', 
            'hadirHariIni', 
            'izinSakit'
        ));
    } // <--- KURUNG KURAWAL INI TADI HILANG

    public function create()
    {
        $karyawanUserIds = Karyawan::pluck('user_id')->toArray();
        $users = User::whereNotIn('id', $karyawanUserIds)->get();
        
        return view('karyawan.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|unique:karyawans,user_id',
            'nama_lengkap' => 'required',
            'jabatan' => 'required',
            'nip' => 'required|unique:karyawans,nip',
            'no_hp' => 'required|unique:karyawans,no_hp',
        ], [
            'user_id.unique' => 'Akun ini sudah terdaftar sebagai karyawan.',
            'nip.unique' => 'NIP ini sudah digunakan oleh karyawan lain.',
            'no_hp.unique' => 'Nomor WhatsApp ini sudah terdaftar di sistem.',
        ]);

        Karyawan::create($request->all());

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        
        // Filter agar user yang sedang diedit tetap muncul, 
        // tapi user lain yang sudah jadi karyawan tidak muncul
        $karyawanUserIds = Karyawan::where('id', '!=', $id)->pluck('user_id')->toArray();
        $users = User::whereNotIn('id', $karyawanUserIds)->get();
        
        return view('karyawan.edit', compact('karyawan', 'users'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'user_id' => 'required|unique:karyawans,user_id,' . $id,
            'nama_lengkap' => 'required',
            'jabatan' => 'required',
            'nip' => 'required|unique:karyawans,nip,' . $id,
            'no_hp' => 'required|unique:karyawans,no_hp,' . $id,
        ]);

        $karyawan->update($request->all());

        return redirect()->route('karyawan.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan telah dihapus dari sistem.');
    }

    public function pimpinanIndex()
    {
        $hariIni = Carbon::now('Asia/Jakarta')->toDateString();
        $bulanIni = Carbon::now('Asia/Jakarta')->month;
        $tahunIni = Carbon::now('Asia/Jakarta')->year;

        $absensiHariIni = Absensi::with('karyawan')
                            ->whereDate('tanggal', $hariIni)
                            ->get();

        $totalHadir = $absensiHariIni->whereIn('status', ['Hadir', 'Terlambat', 'Selesai'])->count();
        $totalIzin = $absensiHariIni->where('status', 'Izin')->count();
        $totalSakit = $absensiHariIni->where('status', 'Sakit')->count();

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