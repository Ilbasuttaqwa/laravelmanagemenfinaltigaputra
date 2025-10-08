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
            // Drop columns yang tidak diperlukan
            $table->dropColumn(['tanggal', 'jam_masuk', 'jam_keluar', 'catatan']);
            
            // Add relation to pembibitan
            $table->unsignedBigInteger('pembibitan_id')->after('id');
            $table->foreign('pembibitan_id')->references('id')->on('pembibitans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['pembibitan_id']);
            $table->dropColumn('pembibitan_id');
            
            // Add back original columns
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->text('catatan')->nullable();
        });
    }
};