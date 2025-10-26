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
            // Drop foreign key constraint first
            $table->dropForeign(['employee_id']);
            
            // Make employee_id nullable
            $table->unsignedBigInteger('employee_id')->nullable()->change();
            
            // Add foreign key constraint back (nullable)
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_reports', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['employee_id']);
            
            // Make employee_id not nullable
            $table->unsignedBigInteger('employee_id')->nullable(false)->change();
            
            // Add foreign key constraint back
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }
};