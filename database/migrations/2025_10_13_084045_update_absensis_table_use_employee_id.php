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
        // Drop existing foreign keys
        Schema::table('absensis', function (Blueprint $table) {
            if (Schema::hasColumn('absensis', 'gudang_id')) {
                $table->dropForeign(['gudang_id']);
            }
            if (Schema::hasColumn('absensis', 'mandor_id')) {
                $table->dropForeign(['mandor_id']);
            }
        });

        // Drop existing columns
        Schema::table('absensis', function (Blueprint $table) {
            if (Schema::hasColumn('absensis', 'gudang_id')) {
                $table->dropColumn('gudang_id');
            }
            if (Schema::hasColumn('absensis', 'mandor_id')) {
                $table->dropColumn('mandor_id');
            }
        });

        // Add employee_id column
        Schema::table('absensis', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('id');
        });

        // Add foreign key
        Schema::table('absensis', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });

        // Drop employee_id column
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });

        // Restore original columns
        Schema::table('absensis', function (Blueprint $table) {
            $table->unsignedBigInteger('gudang_id')->nullable()->after('id');
            $table->unsignedBigInteger('mandor_id')->nullable()->after('gudang_id');
        });

        // Restore foreign keys
        Schema::table('absensis', function (Blueprint $table) {
            $table->foreign('gudang_id')->references('id')->on('gudangs')->onDelete('set null');
            $table->foreign('mandor_id')->references('id')->on('mandors')->onDelete('set null');
        });
    }
};
