<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    /**
     * Pastikan semua kolom yang dikirim dari controller terdaftar di sini.
     */
    protected $fillable = [
        'karyawan_id',
        'user_id',     
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'latitude',    // Penting untuk fitur GPS PT Saltek
        'longitude',   // Penting untuk fitur GPS PT Saltek
        'keterangan',
    ];

    /**
     * Menghubungkan data Absensi ke User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Menghubungkan data Absensi ke Karyawan.
     * Digunakan untuk menampilkan nama di Dashboard Aktivitas Terbaru.
     */
    public function karyawan(): BelongsTo
    {
        // Jika di database kamu menggunakan 'nama_lengkap', gunakan itu di withDefault
        return $this->belongsTo(Karyawan::class, 'karyawan_id')->withDefault([
            'nama_lengkap' => 'Data Karyawan Terhapus'
        ]);
    }
}