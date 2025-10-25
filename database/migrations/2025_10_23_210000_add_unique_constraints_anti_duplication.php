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
        // Add unique constraints to prevent data duplication
        
        // 1. Absensi - Prevent duplicate attendance for same employee on same date
        Schema::table('absensis', function (Blueprint $table) {
            $table->unique(['nama_karyawan', 'tanggal'], 'unique_employee_date');
        });
        
        // 2. Employees - Prevent duplicate employee names
        Schema::table('employees', function (Blueprint $table) {
            $table->unique('nama', 'unique_employee_name');
        });
        
        // 3. Gudang - Prevent duplicate gudang names
        Schema::table('gudangs', function (Blueprint $table) {
            $table->unique('nama', 'unique_gudang_name');
        });
        
        // 4. Mandor - Prevent duplicate mandor names
        Schema::table('mandors', function (Blueprint $table) {
            $table->unique('nama', 'unique_mandor_name');
        });
        
        // 5. Lokasi - Prevent duplicate location names
        Schema::table('lokasis', function (Blueprint $table) {
            $table->unique('nama_lokasi', 'unique_lokasi_name');
        });
        
        // 6. Kandang - Prevent duplicate kandang names within same location
        Schema::table('kandangs', function (Blueprint $table) {
            $table->unique(['nama_kandang', 'lokasi_id'], 'unique_kandang_per_lokasi');
        });
        
        // 7. Pembibitan - Prevent duplicate pembibitan titles within same kandang
        Schema::table('pembibitans', function (Blueprint $table) {
            $table->unique(['judul', 'kandang_id'], 'unique_pembibitan_per_kandang');
        });
        
        // 8. Salary Reports - Prevent duplicate reports for same employee, month, year
        Schema::table('salary_reports', function (Blueprint $table) {
            $table->unique(['employee_id', 'tahun', 'bulan'], 'unique_salary_report_per_employee_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique('unique_employee_date');
        });
        
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique('unique_employee_name');
        });
        
        Schema::table('gudangs', function (Blueprint $table) {
            $table->dropUnique('unique_gudang_name');
        });
        
        Schema::table('mandors', function (Blueprint $table) {
            $table->dropUnique('unique_mandor_name');
        });
        
        Schema::table('lokasis', function (Blueprint $table) {
            $table->dropUnique('unique_lokasi_name');
        });
        
        Schema::table('kandangs', function (Blueprint $table) {
            $table->dropUnique('unique_kandang_per_lokasi');
        });
        
        Schema::table('pembibitans', function (Blueprint $table) {
            $table->dropUnique('unique_pembibitan_per_kandang');
        });
        
        Schema::table('salary_reports', function (Blueprint $table) {
            $table->dropUnique('unique_salary_report_per_employee_month');
        });
    }
};
