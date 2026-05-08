<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel karyawans dan users
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            
            $table->string('tanggal'); // Menyimpan tanggal (format string agar stabil di SQLite)
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable(); // Nama kolom harus jam_pulang agar sinkron dengan Controller
            
            // Status: Hadir, Terlambat, Izin, Sakit, Selesai
            $table->string('status'); 
            
            // Kolom GPS untuk radius 20 meter
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            
            $table->text('keterangan')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};