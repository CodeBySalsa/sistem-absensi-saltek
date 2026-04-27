<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Pastikan User yang login punya relasi ke karyawan
        $karyawan = Auth::user()->karyawan;
        
        $totalHadir = 0;
        if ($karyawan) {
            // Menghitung jumlah record di tabel absensis milik karyawan ini di bulan sekarang
            $totalHadir = Absensi::where('karyawan_id', $karyawan->id)
                                ->whereMonth('tanggal', date('m'))
                                ->count();
        }

        // Kirim variabel $totalHadir ke file dashboard.blade.php
        return view('dashboard', compact('totalHadir'));
    }
}