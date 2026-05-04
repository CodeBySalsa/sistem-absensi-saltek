<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang didefinisikan secara eksplisit.
     */
    protected $table = 'absensis';

    /**
     * mass assignment protection.
     * Pastikan semua kolom yang dikirim dari controller terdaftar di sini.
     */
    protected $fillable = [
        'karyawan_id',
        'user_id',     
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',      // Digunakan untuk: Hadir, Terlambat, Izin, Sakit, Selesai
        'latitude',    // Penting untuk fitur koordinat lokasi PT Saltek
        'longitude',   // Penting untuk fitur koordinat lokasi PT Saltek
        'keterangan',  // Menyimpan alasan atau catatan sistem (Tepat Waktu/Terlambat)
    ];

    /**
     * Menghubungkan data Absensi ke User.
     * Digunakan jika ingin mengambil data user langsung dari tabel absensi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Menghubungkan data Absensi ke profil Karyawan.
     * Relasi ini krusial untuk menampilkan nama karyawan di Dashboard Admin
     * dan Monitoring Aktivitas Terbaru.
     */
    public function karyawan(): BelongsTo
    {
        /**
         * withDefault() mencegah error "attempt to read property on null" 
         * jika data karyawan tidak sengaja terhapus di database.
         */
        return $this->belongsTo(Karyawan::class, 'karyawan_id')->withDefault([
            'nama_lengkap' => 'Data Karyawan Terhapus'
        ]);
    }
}