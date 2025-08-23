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
        Schema::create('siswa_ekskul', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa');
            $table->unsignedBigInteger('id_ekskul');
            $table->string('jabatan', 30)->nullable();     // Ketua, anggota, dll
            $table->string('periode', 20)->nullable();     // Semester atau tahun ajaran
            $table->string('tahun_ajaran', 10)->nullable(); // 2024/2025
            $table->enum('semester', ['ganjil', 'genap'])->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status_keaktifan', ['aktif', 'non_aktif', 'graduated'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
    
            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('id_ekskul')->references('id')->on('ekstrakurikuler')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_ekskul');
    }
};
