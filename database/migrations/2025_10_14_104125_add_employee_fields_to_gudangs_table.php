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
            // Add employee fields to gudangs table
            $table->string('nama')->nullable()->after('id');
            $table->decimal('gaji', 15, 2)->nullable()->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gudangs', function (Blueprint $table) {
            // Remove employee fields
            $table->dropColumn(['nama', 'gaji']);
        });
    }
};
