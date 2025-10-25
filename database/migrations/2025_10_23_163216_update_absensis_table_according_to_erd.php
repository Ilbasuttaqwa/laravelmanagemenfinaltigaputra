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
            // Hapus kolom yang tidak sesuai ERD
            $table->dropColumn(['source_type', 'source_id', 'role_karyawan', 'gaji_karyawan']);
            
            // Tambah kolom sesuai ERD (hanya yang belum ada)
            $table->unsignedBigInteger('pembibitan_id')->nullable()->after('employee_id');
            $table->decimal('gaji_pokok_saat_itu', 15, 2)->nullable()->after('nama_karyawan');
            
            // Tambah foreign key untuk pembibitan
            $table->foreign('pembibitan_id')->references('id')->on('pembibitans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Hapus foreign key
            $table->dropForeign(['pembibitan_id']);
            
            // Hapus kolom yang ditambahkan
            $table->dropColumn(['pembibitan_id', 'gaji_pokok_saat_itu']);
            
            // Kembalikan kolom lama
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('role_karyawan')->nullable();
            $table->decimal('gaji_karyawan', 15, 2)->nullable();
        });
    }
};