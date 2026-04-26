<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        // Mengambil semua data karyawan dari database
        $karyawans = Karyawan::all();

        // Mengirim data ke tampilan (view)
        return view('karyawan.index', compact('karyawans'));
    }
}