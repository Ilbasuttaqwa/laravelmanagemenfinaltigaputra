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
            // Add new columns for integrated data
            $table->string('source_type')->nullable()->after('employee_id'); // 'employee', 'gudang', 'mandor'
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type'); // ID from source table
            $table->string('nama_karyawan')->nullable()->after('source_id'); // Store nama directly
            $table->string('role_karyawan')->nullable()->after('nama_karyawan'); // Store role directly
            $table->decimal('gaji_karyawan', 15, 2)->nullable()->after('role_karyawan'); // Store gaji directly
            
            // Make employee_id nullable since we now have source_type and source_id
            $table->unsignedBigInteger('employee_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['source_type', 'source_id', 'nama_karyawan', 'role_karyawan', 'gaji_karyawan']);
            
            // Make employee_id required again
            $table->unsignedBigInteger('employee_id')->nullable(false)->change();
        });
    }
};