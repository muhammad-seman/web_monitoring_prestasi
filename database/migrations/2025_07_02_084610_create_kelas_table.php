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
    Schema::create('kelas', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kelas', 30);        // Contoh: X IPA 1
        $table->unsignedBigInteger('id_wali_kelas')->nullable()->comment('User id guru wali kelas');
        $table->string('tahun_ajaran', 10)->nullable(); // Contoh: 2024/2025
        $table->timestamps();

        // $table->foreign('id_wali_kelas')->references('id')->on('users')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
