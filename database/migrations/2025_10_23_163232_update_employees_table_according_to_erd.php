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
        Schema::table('employees', function (Blueprint $table) {
            // Rename kolom sesuai ERD
            $table->renameColumn('gaji', 'gaji_pokok');
            $table->renameColumn('role', 'jabatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Kembalikan nama kolom
            $table->renameColumn('gaji_pokok', 'gaji');
            $table->renameColumn('jabatan', 'role');
        });
    }
};