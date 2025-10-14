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
        Schema::create('unified_employees', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->decimal('gaji', 15, 2);
            $table->string('role'); // 'karyawan', 'karyawan_gudang', 'mandor'
            $table->string('source_type'); // 'employee', 'gudang', 'mandor'
            $table->unsignedBigInteger('source_id'); // ID from original table
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['role', 'source_type']);
            $table->unique(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unified_employees');
    }
};