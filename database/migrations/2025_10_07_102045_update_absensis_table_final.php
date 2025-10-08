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
        // First, drop the failed migration
        Schema::table('absensis', function (Blueprint $table) {
            // Check if columns exist before dropping
            if (Schema::hasColumn('absensis', 'gudang_id')) {
                $table->dropForeign(['gudang_id']);
            }
            if (Schema::hasColumn('absensis', 'mandor_id')) {
                $table->dropForeign(['mandor_id']);
            }
            if (Schema::hasColumn('absensis', 'kandang_id')) {
                $table->dropForeign(['kandang_id']);
            }
            if (Schema::hasColumn('absensis', 'lokasi_id')) {
                $table->dropForeign(['lokasi_id']);
            }
            if (Schema::hasColumn('absensis', 'pembibitan_id')) {
                $table->dropForeign(['pembibitan_id']);
            }
        });

        // Drop existing columns if they exist
        Schema::table('absensis', function (Blueprint $table) {
            $columnsToDrop = ['nama', 'gaji', 'gudang_id', 'mandor_id', 'kandang_id', 'lokasi_id', 'pembibitan_id'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('absensis', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Add new structure
        Schema::table('absensis', function (Blueprint $table) {
            $table->unsignedBigInteger('gudang_id')->nullable()->after('id');
            $table->unsignedBigInteger('mandor_id')->nullable()->after('gudang_id');
            $table->date('tanggal')->after('mandor_id');
            $table->enum('status', ['full', 'setengah_hari'])->after('tanggal');
        });

        // Add foreign keys
        Schema::table('absensis', function (Blueprint $table) {
            $table->foreign('gudang_id')->references('id')->on('gudangs')->onDelete('set null');
            $table->foreign('mandor_id')->references('id')->on('mandors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['gudang_id']);
            $table->dropForeign(['mandor_id']);
            $table->dropColumn(['gudang_id', 'mandor_id', 'tanggal', 'status']);
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->string('nama')->after('id');
            $table->decimal('gaji', 10, 2)->after('nama');
            $table->unsignedBigInteger('gudang_id')->nullable()->after('gaji');
            $table->unsignedBigInteger('mandor_id')->nullable()->after('gudang_id');
            $table->unsignedBigInteger('kandang_id')->nullable()->after('mandor_id');
            $table->unsignedBigInteger('lokasi_id')->nullable()->after('kandang_id');
            $table->unsignedBigInteger('pembibitan_id')->nullable()->after('lokasi_id');
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->foreign('gudang_id')->references('id')->on('gudangs')->onDelete('set null');
            $table->foreign('mandor_id')->references('id')->on('mandors')->onDelete('set null');
            $table->foreign('kandang_id')->references('id')->on('kandangs')->onDelete('set null');
            $table->foreign('lokasi_id')->references('id')->on('lokasis')->onDelete('set null');
            $table->foreign('pembibitan_id')->references('id')->on('pembibitans')->onDelete('set null');
        });
    }
};