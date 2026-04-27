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
        Schema::table('absensis', function (Blueprint $table) {
            // Menambah kolom jam_pulang setelah jam_masuk
            // nullable() artinya boleh kosong karena orang baru isi pas sore
            $table->time('jam_pulang')->nullable()->after('jam_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Menghapus kembali kolom jika migrasi di-rollback
            $table->dropColumn('jam_pulang');
        });
    }
};