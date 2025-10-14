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
        Schema::table('monthly_attendance_reports', function (Blueprint $table) {
            // Ubah enum tipe_karyawan untuk mendukung 'karyawan' dan 'mandor'
            $table->enum('tipe_karyawan', ['karyawan', 'mandor'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_attendance_reports', function (Blueprint $table) {
            // Kembalikan ke enum asli
            $table->enum('tipe_karyawan', ['gudang', 'mandor'])->change();
        });
    }
};