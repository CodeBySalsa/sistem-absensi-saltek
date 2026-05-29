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
    Schema::table('karyawans', function (Blueprint $table) {
        // Menambahkan constraint unique pada kolom yang sudah ada
        $table->unique('no_hp'); 
        $table->unique('user_id');
    });
}

public function down(): void
{
    Schema::table('karyawans', function (Blueprint $table) {
        $table->dropUnique(['no_hp']);
        $table->dropUnique(['user_id']);
    });
}
};
