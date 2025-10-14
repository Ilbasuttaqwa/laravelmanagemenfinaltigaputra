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
        Schema::create('salary_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('lokasi_id')->nullable();
            $table->unsignedBigInteger('kandang_id')->nullable();
            $table->unsignedBigInteger('pembibitan_id')->nullable();
            $table->string('nama_karyawan');
            $table->enum('tipe_karyawan', ['karyawan', 'mandor']);
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('jml_hari_kerja', 5, 2);
            $table->decimal('total_gaji', 15, 2);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->timestamps();
            
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('lokasi_id')->references('id')->on('lokasis')->onDelete('set null');
            $table->foreign('kandang_id')->references('id')->on('kandangs')->onDelete('set null');
            $table->foreign('pembibitan_id')->references('id')->on('pembibitans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_reports');
    }
};
