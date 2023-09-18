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
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->integer('point');
            $table->unsignedBigInteger('id_kuis')->index();
            $table->unsignedBigInteger('id_mahasiswa')->index();
            $table->foreign('id_kuis')->on('kuis')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_mahasiswa')->on('mahasiswa')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
