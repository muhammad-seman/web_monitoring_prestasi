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
        Schema::create('kenaikan_kelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa');
            $table->unsignedBigInteger('kelas_asal');
            $table->unsignedBigInteger('kelas_tujuan');
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->enum('status', ['naik', 'tidak_naik', 'pending'])->default('pending');
            $table->json('kriteria_kelulusan')->nullable()->comment('Store criteria met');
            $table->date('tanggal_kenaikan')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('kelas_asal')->references('id')->on('kelas')->onDelete('restrict');
            $table->foreign('kelas_tujuan')->references('id')->on('kelas')->onDelete('restrict');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            // Composite unique to prevent duplicate entries
            $table->unique(['id_siswa', 'tahun_ajaran_id'], 'unique_siswa_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kenaikan_kelas');
    }
};
