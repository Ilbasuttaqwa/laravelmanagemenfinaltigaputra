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
            $table->unsignedBigInteger('gudang_id')->nullable();
            $table->unsignedBigInteger('mandor_id')->nullable();
            $table->unsignedBigInteger('lokasi_id')->nullable();
            $table->unsignedBigInteger('kandang_id')->nullable();
            $table->unsignedBigInteger('pembibitan_id')->nullable();
            $table->string('nama_karyawan');
            $table->string('tipe_karyawan'); // 'gudang' or 'mandor'
            $table->decimal('gaji_pokok', 15, 2);
            $table->integer('jml_hari_kerja'); // jumlah hari kerja (jml mask)
            $table->decimal('total_gaji', 15, 2);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->timestamps();

            $table->foreign('gudang_id')->references('id')->on('gudangs')->onDelete('cascade');
            $table->foreign('mandor_id')->references('id')->on('mandors')->onDelete('cascade');
            $table->foreign('lokasi_id')->references('id')->on('lokasis')->onDelete('cascade');
            $table->foreign('kandang_id')->references('id')->on('kandangs')->onDelete('cascade');
            $table->foreign('pembibitan_id')->references('id')->on('pembibitans')->onDelete('cascade');
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