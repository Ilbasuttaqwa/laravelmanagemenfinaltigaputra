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
        Schema::table('salary_reports', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['gudang_id']);
            $table->dropForeign(['mandor_id']);
            
            // Drop old columns
            $table->dropColumn(['gudang_id', 'mandor_id']);
            
            // Add new employee_id column
            $table->unsignedBigInteger('employee_id')->after('id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            
            // Update tipe_karyawan enum to support 'karyawan' and 'mandor'
            $table->string('tipe_karyawan')->change(); // Change from enum to string for flexibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_reports', function (Blueprint $table) {
            // Drop new foreign key constraint
            $table->dropForeign(['employee_id']);
            
            // Drop new column
            $table->dropColumn('employee_id');
            
            // Add back old columns
            $table->unsignedBigInteger('gudang_id')->nullable();
            $table->unsignedBigInteger('mandor_id')->nullable();
            
            // Add back foreign key constraints
            $table->foreign('gudang_id')->references('id')->on('gudangs')->onDelete('cascade');
            $table->foreign('mandor_id')->references('id')->on('mandors')->onDelete('cascade');
        });
    }
};