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
        Schema::create('sesi_pertemuan', function (Blueprint $table) {
            $table->id();
            $table->integer('pertemuan');
            $table->unsignedBigInteger('id_kelas')->index();
            $table->unsignedBigInteger('id_makul')->index();
            $table->date('tanggal');
            $table->boolean('tambahan')->default(false);
            $table->foreign('id_kelas')->on('kelas')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_makul')->on('mata_kuliah')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('sesi_pertemuan');
    }
};
