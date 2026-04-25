<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel absensis.
     */
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel karyawans
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            
            $table->date('tanggal'); // Menyimpan tanggal (YYYY-MM-DD)
            $table->time('jam_masuk')->nullable(); // Jam klik masuk
            $table->time('jam_keluar')->nullable(); // Jam klik pulang
            
            // Status: Hadir, Terlambat, Izin, Alpa
            $table->string('status'); 
            
            // Keterangan tambahan (misal: alasan izin atau lokasi)
            $table->text('keterangan')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};