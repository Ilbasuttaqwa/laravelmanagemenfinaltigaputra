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
        Schema::create('pembibitans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pembibitan');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->integer('kapasitas')->nullable();
            $table->decimal('luas_lahan', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembibitans');
    }
};