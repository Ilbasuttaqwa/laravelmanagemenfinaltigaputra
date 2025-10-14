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
        Schema::create('monthly_attendance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karyawan'); // Nama dari relasi karyawan gudang atau mandor
            $table->enum('tipe_karyawan', ['karyawan', 'mandor']); // Jenis karyawan
            $table->unsignedBigInteger('karyawan_id'); // ID dari tabel gudang atau mandor
            $table->year('tahun');
            $table->integer('bulan'); // 1-12
            $table->json('data_absensi'); // JSON berisi data absensi per hari dalam bulan
            $table->integer('total_hari_kerja')->default(0);
            $table->integer('total_hari_full')->default(0);
            $table->integer('total_hari_setengah')->default(0);
            $table->integer('total_hari_absen')->default(0);
            $table->decimal('persentase_kehadiran', 5, 2)->default(0);
            $table->timestamps();

            // Index untuk pencarian yang efisien
            $table->index(['tahun', 'bulan']);
            $table->index(['tipe_karyawan', 'karyawan_id']);
            $table->index(['nama_karyawan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_attendance_reports');
    }
};