<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Karyawan extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'karyawans';

    /**
     * Menggunakan guarded kosong agar semua kolom (nip, nama_lengkap, jabatan, no_hp, user_id)
     * bisa diisi secara massal sesuai kebutuhan sistem PT Saltek.
     */
    protected $guarded = [];

    /**
     * Relasi ke User: Menghubungkan profil karyawan dengan akun login.
     * Ditambahkan withDefault agar aplikasi tidak error jika user terkait hilang.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'name' => 'Akun Tidak Ditemukan',
            'email' => '-'
        ]);
    }

    /**
     * Relasi ke Absensi (PENTING):
     * Satu Karyawan memiliki banyak data Absensi.
     * Digunakan oleh DashboardController untuk fitur withCount (Total Hadir, Izin, Sakit).
     */
    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'karyawan_id');
    }
}