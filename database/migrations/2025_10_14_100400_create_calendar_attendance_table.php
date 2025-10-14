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
        Schema::create('calendar_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gudang_id')->nullable();
            $table->unsignedBigInteger('mandor_id')->nullable();
            $table->year('tahun');
            $table->integer('bulan'); // 1-12
            $table->json('attendance_data'); // JSON berisi data absensi per hari
            $table->timestamps();

            // Foreign keys
            $table->foreign('gudang_id')->references('id')->on('gudangs')->onDelete('cascade');
            $table->foreign('mandor_id')->references('id')->on('mandors')->onDelete('cascade');

            // Index untuk pencarian yang efisien
            $table->index(['tahun', 'bulan']);
            $table->index(['gudang_id', 'tahun', 'bulan']);
            $table->index(['mandor_id', 'tahun', 'bulan']);
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['gudang_id', 'mandor_id', 'tahun', 'bulan'], 'unique_employee_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_attendances');
    }
};