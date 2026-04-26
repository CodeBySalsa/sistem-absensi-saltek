<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat akun login untuk Budi
        $user = User::create([
            'name' => 'Budi Saltek',
            'email' => 'budi@saltek.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. Buat data profil Karyawan dan hubungkan ke ID user di atas
        Karyawan::create([
            'user_id'      => $user->id, // Ini kuncinya!
            'nip'          => '2026001',
            'nama_lengkap' => 'Budi Saltek',
            'jabatan'      => 'Teknisi Fiber Optik',
            'no_hp'        => '081234567890',
        ]);
    }
}