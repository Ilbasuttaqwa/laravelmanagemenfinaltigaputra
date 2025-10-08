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
        Schema::table('kandangs', function (Blueprint $table) {
            // Drop existing foreign key and column
            $table->dropForeign(['karyawan_id']);
            $table->dropColumn('karyawan_id');
            
            // Add new employee_id foreign key
            $table->unsignedBigInteger('employee_id')->nullable()->after('lokasi');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kandangs', function (Blueprint $table) {
            // Drop new foreign key and column
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
            
            // Add back karyawan_id
            $table->unsignedBigInteger('karyawan_id')->nullable()->after('lokasi');
            $table->foreign('karyawan_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
