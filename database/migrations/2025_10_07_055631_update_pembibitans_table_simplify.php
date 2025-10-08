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
        Schema::table('pembibitans', function (Blueprint $table) {
            // Drop columns yang tidak diperlukan
            $table->dropColumn(['deskripsi', 'status', 'kapasitas', 'luas_lahan']);
            
            // Add columns yang diperlukan
            $table->string('judul')->after('id');
            $table->unsignedBigInteger('lokasi_id')->nullable()->after('judul');
            $table->unsignedBigInteger('kandang_id')->nullable()->after('lokasi_id');
            $table->date('tanggal_mulai')->after('kandang_id');
            
            // Add foreign keys
            $table->foreign('lokasi_id')->references('id')->on('lokasis')->onDelete('set null');
            $table->foreign('kandang_id')->references('id')->on('kandangs')->onDelete('set null');
        });
        
        // Drop nama_pembibitan column
        Schema::table('pembibitans', function (Blueprint $table) {
            $table->dropColumn('nama_pembibitan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembibitans', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['lokasi_id']);
            $table->dropForeign(['kandang_id']);
            
            // Drop columns
            $table->dropColumn(['judul', 'lokasi_id', 'kandang_id', 'tanggal_mulai']);
            
            // Add back original columns
            $table->string('nama_pembibitan');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->integer('kapasitas')->nullable();
            $table->decimal('luas_lahan', 10, 2)->nullable();
        });
    }
};