<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add performance indexes for large datasets
     */
    public function up(): void
    {
        // Add indexes to absensis table for better performance
        Schema::table('absensis', function (Blueprint $table) {
            // Index for employee_id and tanggal (most common query)
            $table->index(['employee_id', 'tanggal'], 'idx_absensi_employee_date');
            
            // Index for tanggal only (for date range queries)
            $table->index('tanggal', 'idx_absensi_tanggal');
            
            // Index for status (for filtering by status)
            $table->index('status', 'idx_absensi_status');
            
            // Index for nama_karyawan (for name searches)
            $table->index('nama_karyawan', 'idx_absensi_nama');
            
            // Index for pembibitan_id (for pembibitan filtering)
            $table->index('pembibitan_id', 'idx_absensi_pembibitan');
            
            // Index for created_at (for recent records)
            $table->index('created_at', 'idx_absensi_created');
        });
        
        // Add indexes to employees table
        Schema::table('employees', function (Blueprint $table) {
            // Index for jabatan (for role filtering)
            $table->index('jabatan', 'idx_employees_jabatan');
            
            // Index for kandang_id (for kandang filtering)
            $table->index('kandang_id', 'idx_employees_kandang');
            
            // Index for nama (for name searches)
            $table->index('nama', 'idx_employees_nama');
        });
        
        // Add indexes to salary_reports table
        Schema::table('salary_reports', function (Blueprint $table) {
            // Index for employee_id, tahun, bulan (most common query)
            $table->index(['employee_id', 'tahun', 'bulan'], 'idx_salary_employee_period');
            
            // Index for tahun, bulan (for period queries)
            $table->index(['tahun', 'bulan'], 'idx_salary_period');
            
            // Index for tipe_karyawan (for type filtering)
            $table->index('tipe_karyawan', 'idx_salary_tipe');
            
            // Index for pembibitan_id (for pembibitan filtering)
            $table->index('pembibitan_id', 'idx_salary_pembibitan');
        });
        
        // Add indexes to pembibitans table
        Schema::table('pembibitans', function (Blueprint $table) {
            // Index for kandang_id (for kandang filtering)
            $table->index('kandang_id', 'idx_pembibitan_kandang');
            
            // Index for lokasi_id (for location filtering)
            $table->index('lokasi_id', 'idx_pembibitan_lokasi');
            
            // Index for judul (for title searches)
            $table->index('judul', 'idx_pembibitan_judul');
        });
        
        // Add indexes to kandangs table
        Schema::table('kandangs', function (Blueprint $table) {
            // Index for lokasi_id (for location filtering)
            $table->index('lokasi_id', 'idx_kandang_lokasi');
            
            // Index for nama_kandang (for name searches)
            $table->index('nama_kandang', 'idx_kandang_nama');
        });
        
        // Add indexes to gudangs table
        Schema::table('gudangs', function (Blueprint $table) {
            // Index for nama (for name searches)
            $table->index('nama', 'idx_gudang_nama');
        });
        
        // Add indexes to mandors table
        Schema::table('mandors', function (Blueprint $table) {
            // Index for nama (for name searches)
            $table->index('nama', 'idx_mandor_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from absensis table
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex('idx_absensi_employee_date');
            $table->dropIndex('idx_absensi_tanggal');
            $table->dropIndex('idx_absensi_status');
            $table->dropIndex('idx_absensi_nama');
            $table->dropIndex('idx_absensi_pembibitan');
            $table->dropIndex('idx_absensi_created');
        });
        
        // Remove indexes from employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('idx_employees_jabatan');
            $table->dropIndex('idx_employees_kandang');
            $table->dropIndex('idx_employees_nama');
        });
        
        // Remove indexes from salary_reports table
        Schema::table('salary_reports', function (Blueprint $table) {
            $table->dropIndex('idx_salary_employee_period');
            $table->dropIndex('idx_salary_period');
            $table->dropIndex('idx_salary_tipe');
            $table->dropIndex('idx_salary_pembibitan');
        });
        
        // Remove indexes from pembibitans table
        Schema::table('pembibitans', function (Blueprint $table) {
            $table->dropIndex('idx_pembibitan_kandang');
            $table->dropIndex('idx_pembibitan_lokasi');
            $table->dropIndex('idx_pembibitan_judul');
        });
        
        // Remove indexes from kandangs table
        Schema::table('kandangs', function (Blueprint $table) {
            $table->dropIndex('idx_kandang_lokasi');
            $table->dropIndex('idx_kandang_nama');
        });
        
        // Remove indexes from gudangs table
        Schema::table('gudangs', function (Blueprint $table) {
            $table->dropIndex('idx_gudang_nama');
        });
        
        // Remove indexes from mandors table
        Schema::table('mandors', function (Blueprint $table) {
            $table->dropIndex('idx_mandor_nama');
        });
    }
};
