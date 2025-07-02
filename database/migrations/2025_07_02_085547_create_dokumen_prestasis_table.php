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
    Schema::create('dokumen_prestasi', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_prestasi_siswa');
        $table->string('nama_file', 255);
        $table->string('path_file', 255);           // Path penyimpanan file, bisa local atau cloud
        $table->unsignedBigInteger('uploaded_by')->nullable();
        $table->timestamp('uploaded_at')->nullable();
        $table->timestamps();

        $table->foreign('id_prestasi_siswa')->references('id')->on('prestasi_siswa')->onDelete('cascade');
        $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_prestasi');
    }
};
