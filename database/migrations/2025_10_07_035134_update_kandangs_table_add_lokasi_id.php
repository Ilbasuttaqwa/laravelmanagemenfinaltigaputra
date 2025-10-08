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
            // Add lokasi_id foreign key
            $table->unsignedBigInteger('lokasi_id')->nullable()->after('nama_kandang');
            $table->foreign('lokasi_id')->references('id')->on('lokasis')->onDelete('set null');
            
            // Remove old lokasi column
            $table->dropColumn('lokasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kandangs', function (Blueprint $table) {
            // Add back lokasi column
            $table->string('lokasi')->after('nama_kandang');
            
            // Drop foreign key and lokasi_id
            $table->dropForeign(['lokasi_id']);
            $table->dropColumn('lokasi_id');
        });
    }
};
