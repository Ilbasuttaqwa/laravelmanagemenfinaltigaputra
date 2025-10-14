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
        Schema::table('gudangs', function (Blueprint $table) {
            // Drop foreign key constraints first
            if (Schema::hasColumn('gudangs', 'lokasi_id')) {
                $table->dropForeign(['lokasi_id']);
            }
        });
        
        Schema::table('gudangs', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('gudangs', 'nama_gudang')) {
                $table->dropColumn('nama_gudang');
            }
            if (Schema::hasColumn('gudangs', 'lokasi_id')) {
                $table->dropColumn('lokasi_id');
            }
            if (Schema::hasColumn('gudangs', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }
            
            // Ensure nama and gaji columns exist and are not nullable
            if (!Schema::hasColumn('gudangs', 'nama')) {
                $table->string('nama')->after('id');
            }
            if (!Schema::hasColumn('gudangs', 'gaji')) {
                $table->decimal('gaji', 15, 2)->after('nama');
            }
        });
        
        Schema::table('gudangs', function (Blueprint $table) {
            // Make nama and gaji not nullable
            $table->string('nama')->nullable(false)->change();
            $table->decimal('gaji', 15, 2)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gudangs', function (Blueprint $table) {
            // Add back old columns
            $table->string('nama_gudang')->after('id');
            $table->unsignedBigInteger('lokasi_id')->nullable()->after('nama_gudang');
            $table->text('deskripsi')->nullable()->after('lokasi_id');
            
            // Remove new columns
            $table->dropColumn(['nama', 'gaji']);
        });
    }
};