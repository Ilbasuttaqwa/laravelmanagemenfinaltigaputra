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
            // Drop existing foreign key
            $table->dropForeign(['pembibitan_id']);
            $table->dropColumn('pembibitan_id');
        });

        Schema::table('absensis', function (Blueprint $table) {
            // Add new columns
            $table->string('nama')->after('id');
            $table->decimal('gaji', 10, 2)->after('nama');
            $table->unsignedBigInteger('gudang_id')->nullable()->after('gaji');
            $table->unsignedBigInteger('mandor_id')->nullable()->after('gudang_id');
            $table->unsignedBigInteger('kandang_id')->nullable()->after('mandor_id');
            $table->unsignedBigInteger('lokasi_id')->nullable()->after('kandang_id');
            $table->unsignedBigInteger('pembibitan_id')->nullable()->after('lokasi_id');
        });

        Schema::table('absensis', function (Blueprint $table) {
            // Add foreign keys
            $table->foreign('gudang_id')->references('id')->on('gudangs')->onDelete('set null');
            $table->foreign('mandor_id')->references('id')->on('mandors')->onDelete('set null');
            $table->foreign('kandang_id')->references('id')->on('kandangs')->onDelete('set null');
            $table->foreign('lokasi_id')->references('id')->on('lokasis')->onDelete('set null');
            $table->foreign('pembibitan_id')->references('id')->on('pembibitans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['gudang_id']);
            $table->dropForeign(['mandor_id']);
            $table->dropForeign(['kandang_id']);
            $table->dropForeign(['lokasi_id']);
            $table->dropForeign(['pembibitan_id']);
            
            // Drop columns
            $table->dropColumn(['nama', 'gaji', 'gudang_id', 'mandor_id', 'kandang_id', 'lokasi_id', 'pembibitan_id']);
            
            // Add back original column
            $table->unsignedBigInteger('pembibitan_id');
            $table->foreign('pembibitan_id')->references('id')->on('pembibitans')->onDelete('cascade');
        });
    }
};