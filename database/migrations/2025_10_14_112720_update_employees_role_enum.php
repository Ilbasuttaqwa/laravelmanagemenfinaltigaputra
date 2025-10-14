<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the role column to allow longer values
        DB::statement("ALTER TABLE employees MODIFY COLUMN role VARCHAR(50) DEFAULT 'karyawan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        DB::statement("ALTER TABLE employees MODIFY COLUMN role ENUM('karyawan', 'mandor') DEFAULT 'karyawan'");
    }
};