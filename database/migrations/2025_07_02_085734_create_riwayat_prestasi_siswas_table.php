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
        Schema::create('riwayat_prestasi_siswa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_prestasi_siswa');
            $table->enum('status', ['draft', 'menunggu_validasi', 'diterima', 'ditolak']);
            $table->text('keterangan')->nullable();      // Penjelasan, catatan revisi/penolakan
            $table->timestamp('tanggal_perubahan')->useCurrent();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();
    
            $table->foreign('id_prestasi_siswa')->references('id')->on('prestasi_siswa')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_prestasi_siswa');
    }
};
