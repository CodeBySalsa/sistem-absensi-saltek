<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    /**
     * Tabel yang terhubung dengan model ini.
     */
    protected $table = 'absensis';

    /**
     * fillable menentukan kolom mana saja yang boleh diisi secara massal.
     * Ini lebih aman daripada $guarded untuk aplikasi produksi.
     */
    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'keterangan', // Pastikan kolom ini ada untuk menampung alasan Izin/Sakit
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
     * RELASI PENTING: Inilah yang menarik data 'nama' ke tabel Dashboard.
     * withDefault() ditambahkan agar tidak error jika data karyawan terhapus.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id')->withDefault([
            'nama_lengkap' => 'Karyawan Tidak Ditemukan'
        ]);
    }
}