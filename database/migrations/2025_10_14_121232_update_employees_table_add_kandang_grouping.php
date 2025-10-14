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
            // Add kandang_id to group employees by kandang
            $table->unsignedBigInteger('kandang_id')->nullable()->after('role');
            $table->string('lokasi_kerja')->nullable()->after('kandang_id'); // Nama lokasi kerja
            
            // Add foreign key constraint
            $table->foreign('kandang_id')->references('id')->on('kandangs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['kandang_id']);
            
            // Drop columns
            $table->dropColumn(['kandang_id', 'lokasi_kerja']);
        });
    }
};