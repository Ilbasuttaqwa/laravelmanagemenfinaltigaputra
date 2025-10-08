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
        Schema::dropIfExists('kandang_employee');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('kandang_employee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kandang_id');
            $table->unsignedBigInteger('employee_id');
            $table->timestamps();
            
            $table->foreign('kandang_id')->references('id')->on('kandangs')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            
            $table->unique(['kandang_id', 'employee_id']);
        });
    }
};